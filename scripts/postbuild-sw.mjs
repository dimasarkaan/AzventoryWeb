/**
 * Post-build script for PWA Service Worker
 * 
 * Problem: VitePWA generates sw.js in public/build/ but Service Workers
 * must be registered at root (/) to control the entire site scope.
 * Simply using importScripts('/build/sw.js') breaks relative path resolution
 * for the workbox module (./workbox-xxx resolves from / instead of /build/).
 * 
 * Solution: Copy sw.js to public/ root, rewriting relative paths to absolute.
 */
import { readFileSync, writeFileSync, readdirSync } from 'fs';
import { join } from 'path';

const buildDir = join(process.cwd(), 'public', 'build');
const publicDir = join(process.cwd(), 'public');

// Read the generated sw.js
let swContent = readFileSync(join(buildDir, 'sw.js'), 'utf-8');

// Find workbox filename (e.g., workbox-34a8ec49.js)
const workboxFile = readdirSync(buildDir).find(f => f.startsWith('workbox-') && f.endsWith('.js'));

if (workboxFile) {
    const workboxName = workboxFile.replace('.js', '');
    // Replace relative workbox reference with absolute path
    // The AMD loader uses: define(["./workbox-XXXX"], function(e){...})
    swContent = swContent.replace(
        `"./${workboxName}"`,
        `"/build/${workboxName}"`
    );
}

// Also fix precache URLs to be absolute (they are relative to sw.js location)
// e.g., "registerSW.js" -> "/build/registerSW.js"
// But keep URLs that already start with "/" (like "/offline")
swContent = swContent.replace(
    /\{url:"(?!\/|https?:\/\/)([^"]+)"/g,
    '{url:"/build/$1"'
);

writeFileSync(join(publicDir, 'sw.js'), swContent);
console.log('✓ Created public/sw.js with fixed absolute paths');
