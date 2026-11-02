const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const isWin = process.platform === 'win32';
const npmCmd = isWin ? 'npm.cmd' : 'npm';

console.log('🚀 [Build] Starting build process...');

const frontendDir = path.join(__dirname, 'frontend');
const rootDist = path.join(__dirname, 'dist');
const frontendDist = path.join(frontendDir, 'dist');

try {
  // 1. Install frontend dependencies
  console.log('📦 [Build] Installing frontend dependencies...');
  execSync(`${npmCmd} install`, {
    cwd: frontendDir,
    stdio: 'inherit',
    env: process.env
  });

  // 2. Generate Prisma Client if prisma schema exists
  const backendDir = path.join(__dirname, 'backend');
  const prismaSchema = path.join(backendDir, 'prisma', 'schema.prisma');
  if (fs.existsSync(prismaSchema)) {
    console.log('⚡ [Build] Generating Prisma Client...');
    execSync(`npx --yes prisma generate --schema="${prismaSchema}"`, {
      cwd: backendDir,
      stdio: 'inherit',
      env: process.env
    });
  }

  // 3. Build frontend
  console.log('🔨 [Build] Compiling frontend (Vite)...');
  execSync(`${npmCmd} run build`, {
    cwd: frontendDir,
    stdio: 'inherit',
    env: process.env
  });

  // 3. Ensure root dist exists and copy artifacts
  if (fs.existsSync(frontendDist)) {
    if (fs.existsSync(rootDist)) {
      fs.rmSync(rootDist, { recursive: true, force: true });
    }
    fs.cpSync(frontendDist, rootDist, { recursive: true });
    console.log('✨ [Build] Frontend assets successfully built and copied to root /dist');
  }

  // 4. Ensure _redirects file for SPA routing on Cloudflare Pages / Netlify
  const redirectsFile = path.join(rootDist, '_redirects');
  if (!fs.existsSync(redirectsFile)) {
    fs.writeFileSync(redirectsFile, '/* /index.html 200\n');
  }

  console.log('✅ [Build] Build completed successfully!');
} catch (error) {
  console.error('❌ [Build] Build failed:', error);
  process.exit(1);
}
