import { access, cp, lstat, readFile, unlink } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const expectedProject = {
    projectId: 'prj_uGtI05jocA3IsqbDjvKeuslpD4Hj',
    projectName: 'bengkel-gading-jaya-q4rj',
    orgId: 'team_GfJYKPTrNp7DJjpjGArfencc',
};
const vercelScope = 'rizqi239s-kedai-pinky';

async function normalizePublicStorage() {
    const publicStorage = path.join(projectRoot, 'public', 'storage');
    let publicStorageStats;

    try {
        publicStorageStats = await lstat(publicStorage);
    } catch (error) {
        if (error.code === 'ENOENT') {
            return;
        }

        throw error;
    }

    if (! publicStorageStats.isSymbolicLink()) {
        return;
    }

    const storageTarget = path.join(projectRoot, 'storage', 'app', 'public');
    await access(storageTarget);
    await unlink(publicStorage);
    await cp(storageTarget, publicStorage, { recursive: true });
    console.log('Copied public storage into the project before building.');
}

async function normalizeStaticStorage() {
    const outputStorage = path.join(projectRoot, '.vercel', 'output', 'static', 'storage');
    let outputStorageStats;

    try {
        outputStorageStats = await lstat(outputStorage);
    } catch (error) {
        if (error.code === 'ENOENT') {
            return;
        }

        throw error;
    }

    if (! outputStorageStats.isSymbolicLink()) {
        return;
    }

    const publicStorage = path.join(projectRoot, 'storage', 'app', 'public');
    await access(publicStorage);
    await unlink(outputStorage);
    await cp(publicStorage, outputStorage, { recursive: true });
    console.log('Copied public storage into Vercel static output.');
}

function run(command, args) {
    console.log(`\n> ${command} ${args.join(' ')}`);

    const result = spawnSync(command, args, {
        cwd: projectRoot,
        stdio: 'inherit',
    });

    if (result.error) {
        throw new Error(`Could not start "${command}": ${result.error.message}`);
    }

    if (result.status !== 0) {
        process.exitCode = result.status ?? 1;
        throw new Error(`"${command} ${args.join(' ')}" failed.`);
    }
}

async function main() {
    if (process.env.CONFIRM_PRODUCTION_DEPLOY !== 'deploy') {
        throw new Error('Production deployment requires CONFIRM_PRODUCTION_DEPLOY=deploy.');
    }

    if (process.platform !== 'linux') {
        throw new Error('The Vercel PHP build requires Linux. Use the GitHub Actions workflow; no local WSL or Docker is needed.');
    }

    let project;
    try {
        project = JSON.parse(await readFile(path.join(projectRoot, '.vercel', 'project.json'), 'utf8'));
    } catch (error) {
        throw new Error(`Could not read the linked Vercel project at the repository root: ${error.message}`);
    }

    if (
        project.projectId !== expectedProject.projectId ||
        project.projectName !== expectedProject.projectName ||
        project.orgId !== expectedProject.orgId
    ) {
        throw new Error(
            `The linked Vercel project must be ${expectedProject.projectName} (${expectedProject.projectId}) ` +
            `in team ${expectedProject.orgId}; found ${project.projectName ?? 'unknown'} ` +
            `(${project.projectId ?? 'unknown'}) in ${project.orgId ?? 'an unknown team'}.`,
        );
    }

    run('vercel', ['project', 'inspect', '--non-interactive', '--scope', vercelScope]);
    run('vercel', ['pull', '--yes', '--environment', 'production', '--scope', vercelScope]);
    await normalizePublicStorage();
    run('vercel', ['build', '--yes', '--prod', '--scope', vercelScope]);
    await normalizeStaticStorage();
    run('node', ['scripts/patch-vercel-runtime.mjs']);
    run('vercel', ['deploy', '--prebuilt', '--prod', '--yes', '--scope', vercelScope]);
}

main().catch((error) => {
    console.error(`Production deployment stopped: ${error.message}`);
    process.exitCode ||= 1;
});
