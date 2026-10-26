import { readFile } from 'node:fs/promises';
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
    run('vercel', ['build', '--yes', '--prod', '--scope', vercelScope]);
    run('node', ['scripts/patch-vercel-runtime.mjs']);
    run('vercel', ['deploy', '--prebuilt', '--prod', '--yes', '--scope', vercelScope]);
}

main().catch((error) => {
    console.error(`Production deployment stopped: ${error.message}`);
    process.exitCode ||= 1;
});
