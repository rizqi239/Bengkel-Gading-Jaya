import { access, readFile, readdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const expectedHandler = 'launcher.launcher';

const patchedNormalizer = [
    'function isRecord(value) {',
    "    return typeof value === 'object' && value !== null && !Array.isArray(value);",
    '}',
    'function isStringArray(value) {',
    "    return Array.isArray(value) && value.every((item) => typeof item === 'string');",
    '}',
    'function isByteArray(value) {',
    '    return Array.isArray(value) && value.every((item) =>',
    "        typeof item === 'number' && Number.isInteger(item) && item >= 0 && item <= 255);",
    '}',
    'function normalizeBody(body, encoding) {',
    "    if (encoding !== undefined && encoding !== null && encoding !== 'utf8' && encoding !== 'base64') {",
    "        throw new Error('Unsupported encoding: ' + encoding);",
    '    }',
    '    if (body === undefined || body === null)',
    '        return undefined;',
    '    if (Buffer.isBuffer(body))',
    '        return body;',
    '    if (isByteArray(body))',
    '        return Buffer.from(body);',
    "    if (typeof body === 'string')",
    "        return Buffer.from(body, encoding === 'base64' ? 'base64' : 'utf8');",
    "    throw new TypeError('Request body must be a string, Buffer, or array of bytes');",
    '}',
    'function normalizeHeaders(value) {',
    '    if (value === undefined || value === null)',
    '        return {};',
    '    if (!isRecord(value))',
    "        throw new TypeError('Request headers must be an object');",
    '    const headers = {};',
    '    for (const [name, header] of Object.entries(value)) {',
    '        if (header !== undefined && typeof header !== \'string\' && !isStringArray(header))',
    "            throw new TypeError('Invalid request header: ' + name);",
    '        Object.defineProperty(headers, name, { value: header, enumerable: true });',
    '    }',
    '    return headers;',
    '}',
    'function getHeader(headers, name) {',
    '    const key = Object.keys(headers).find((headerName) => headerName.toLowerCase() === name);',
    '    const value = key === undefined ? undefined : headers[key];',
    '    return Array.isArray(value) ? value[0] : value;',
    '}',
    'function normalizeEvent(event) {',
    '    if (!isRecord(event))',
    "        throw new TypeError('Request event must be an object');",
    "    const isInvoke = event.Action === 'Invoke';",
    '    let request = event;',
    '    if (isInvoke) {',
    "        if (typeof event.body !== 'string')",
    "            throw new TypeError('Invoke body must be JSON text');",
    '        const parsed = JSON.parse(event.body);',
    '        if (!isRecord(parsed))',
    "            throw new TypeError('Invoke body must contain a request object');",
    '        request = parsed;',
    '    }',
    '    const method = isInvoke ? request.method : request.httpMethod;',
    '    const { path } = request;',
    "    if (typeof method !== 'string' || typeof path !== 'string')",
    "        throw new TypeError('Request method and path must be strings');",
    "    if (request.host !== undefined && request.host !== null && typeof request.host !== 'string')",
    "        throw new TypeError('Request host must be a string');",
    '    const headers = normalizeHeaders(request.headers);',
    "    const host = request.host || getHeader(headers, 'x-forwarded-host') || getHeader(headers, 'host') || '';",
    '    if (!isInvoke && request.isBase64Encoded !== undefined && request.isBase64Encoded !== null &&',
    "        typeof request.isBase64Encoded !== 'boolean')",
    "        throw new TypeError('isBase64Encoded must be a boolean');",
    "    const encoding = isInvoke ? request.encoding : request.isBase64Encoded === true ? 'base64' : 'utf8';",
    '    return { method, path, host, headers, body: normalizeBody(request.body, encoding) };',
    '}',
    '',
].join('\n');

async function patchTaskRootFallback(functionDirectory) {
    const helpersPath = path.join(functionDirectory, 'helpers.js');
    let helpers = await readFile(helpersPath, 'utf8');

    const oldUserRoot = "process.env.LAMBDA_TASK_ROOT || '/', 'user'";
    const oldPhpRoot = "process.env.LAMBDA_TASK_ROOT || '/', 'php'";
    const newUserRoot = "process.env.LAMBDA_TASK_ROOT || '/var/task', 'user'";
    const newPhpRoot = "process.env.LAMBDA_TASK_ROOT || '/var/task', 'php'";

    const alreadyPatched = helpers.includes(newUserRoot) &&
        helpers.includes(newPhpRoot) &&
        helpers.includes('function normalizeBody(body, encoding)');
    const needsPatch = helpers.includes(oldUserRoot) &&
        helpers.includes(oldPhpRoot) &&
        helpers.includes('function normalizeEvent(event) {');

    if (!alreadyPatched && !needsPatch) {
        throw new Error(`Unexpected task-root/request helper format in ${helpersPath}`);
    }

    if (needsPatch) {
        helpers = helpers
            .replace(oldUserRoot, newUserRoot)
            .replace(oldPhpRoot, newPhpRoot);
        const normalizeStart = helpers.indexOf('function normalizeEvent(event) {');
        const normalizeEnd = helpers.indexOf('async function transformFromAwsRequest', normalizeStart);

        if (normalizeEnd === -1) {
            throw new Error(`Could not locate request helper boundary in ${helpersPath}`);
        }

        helpers = `${helpers.slice(0, normalizeStart)}${patchedNormalizer}${helpers.slice(normalizeEnd)}`;
        await writeFile(helpersPath, helpers);
    }
}

async function findFunctionConfigs(directory) {
    const entries = await readdir(directory, { withFileTypes: true });
    const configs = [];

    for (const entry of entries) {
        const entryPath = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            configs.push(...await findFunctionConfigs(entryPath));
        } else if (entry.isFile() && entry.name === '.vc-config.json') {
            configs.push(entryPath);
        }
    }

    return configs;
}

async function patchConfig(configPath) {
    const config = JSON.parse(await readFile(configPath, 'utf8'));
    const functionDirectory = path.dirname(configPath);

    if (config.handler !== expectedHandler && config.awsLambdaHandler !== expectedHandler) {
        return false;
    }

    if (typeof config.runtime !== 'string' || !config.runtime.startsWith('nodejs')) {
        throw new Error(`Unexpected runtime in ${configPath}: ${config.runtime}`);
    }

    await access(path.join(functionDirectory, 'launcher.js'));

    if (config.handler !== expectedHandler && config.handler !== 'launcher.js') {
        throw new Error(`Unexpected handler in ${configPath}: ${config.handler}`);
    }

    if (config.awsLambdaHandler && config.awsLambdaHandler !== expectedHandler) {
        throw new Error(`Unexpected AWS Lambda handler in ${configPath}: ${config.awsLambdaHandler}`);
    }

    await patchTaskRootFallback(functionDirectory);

    Object.assign(config, {
        handler: 'launcher.js',
        launcherType: 'Nodejs',
        awsLambdaHandler: expectedHandler,
        shouldAddHelpers: false,
        shouldAddSourcemapSupport: false,
    });

    await writeFile(configPath, `${JSON.stringify(config, null, 2)}\n`);
    return true;
}

export async function patchVercelRuntime(outputDirectory = '.vercel/output') {
    const absoluteOutputDirectory = path.resolve(outputDirectory);
    const functionsDirectory = path.join(absoluteOutputDirectory, 'functions');

    await access(functionsDirectory);

    const configs = await findFunctionConfigs(functionsDirectory);
    let patchedCount = 0;

    for (const configPath of configs) {
        if (await patchConfig(configPath)) {
            patchedCount += 1;
            console.log(`Patched ${path.relative(absoluteOutputDirectory, configPath).split(path.sep).join('/')}`);
        }
    }

    if (patchedCount === 0) {
        throw new Error(`No vercel-php launcher function found under ${functionsDirectory}`);
    }
}

if (process.argv[1] && pathToFileURL(path.resolve(process.argv[1])).href === import.meta.url) {
    try {
        await patchVercelRuntime(process.argv[2]);
    } catch (error) {
        console.error(`Vercel PHP runtime patch failed: ${error.message}`);
        process.exitCode = 1;
    }
}
