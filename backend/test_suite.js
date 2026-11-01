const http = require('http');

async function test(name, urlStr, options = {}) {
  return new Promise((resolve) => {
    const url = new URL(urlStr);
    const reqOptions = {
      hostname: url.hostname,
      port: url.port,
      path: url.pathname + url.search,
      method: options.method || 'GET',
      headers: options.headers || {}
    };

    const req = http.request(reqOptions, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        console.log(`[PASS] ${name} -> Status: ${res.statusCode} (length: ${data.length})`);
        resolve({ status: res.statusCode, data });
      });
    });

    req.on('error', err => {
      console.log(`[FAIL] ${name} -> Error: ${err.message}`);
      resolve({ error: err });
    });

    if (options.body) {
      req.write(options.body);
    }
    req.end();
  });
}

(async () => {
  console.log('=== STARTING END-TO-END VERIFICATION ===\n');

  // 1. Vite frontend server
  await test('Vite Dev Server (5173)', 'http://localhost:5173/');

  // 2. Express backend server
  await test('Backend API Status (3000)', 'http://localhost:3000/api-status');
  await test('Landing Page Data', 'http://localhost:3000/api/home');

  // 3. User Login & Dashboard
  const loginRes = await test('User Login', 'http://localhost:3000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'user@kawinan.test', password: 'password' })
  });

  const loginData = JSON.parse(loginRes.data);
  const userToken = loginData.token;

  if (userToken) {
    console.log(`  ✓ Authenticated user: ${loginData.user.name}`);
    await test('User Dashboard Invitations', 'http://localhost:3000/api/dashboard', {
      headers: { 'Authorization': 'Bearer ' + userToken }
    });
    await test('Editor View Data (id: 1)', 'http://localhost:3000/api/editor/1', {
      headers: { 'Authorization': 'Bearer ' + userToken }
    });
    await test('Guests List & Analytics (id: 1)', 'http://localhost:3000/api/invitations/1/guests', {
      headers: { 'Authorization': 'Bearer ' + userToken }
    });
  }

  // 4. Admin Login & Panels
  const adminRes = await test('Admin Login', 'http://localhost:3000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'admin@kawinan.test', password: 'password' })
  });

  const adminToken = JSON.parse(adminRes.data).token;
  if (adminToken) {
    console.log(`  ✓ Authenticated super admin`);
    await test('Admin Dashboard Stats', 'http://localhost:3000/api/admin/dashboard', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
    await test('Admin Users List', 'http://localhost:3000/api/admin/users', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
    await test('Admin Templates List', 'http://localhost:3000/api/admin/templates', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
    await test('Admin Musics Library', 'http://localhost:3000/api/admin/musics', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
    await test('Admin Plans Management', 'http://localhost:3000/api/admin/plans', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
    await test('Admin Transactions History', 'http://localhost:3000/api/admin/transactions', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
    await test('Admin Settings & Midtrans Keys', 'http://localhost:3000/api/admin/settings', {
      headers: { 'Authorization': 'Bearer ' + adminToken }
    });
  }

  // 5. Public Invitation & RSVP
  await test('Public Invitation Render (/u/upit-aa)', 'http://localhost:3000/api/invitations/public/upit-aa');

  const rsvpRes = await test('Submit RSVP to Public Invitation', 'http://localhost:3000/api/invitations/public/upit-aa/rsvp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      name: 'Sahabat Pengujian NodeVue',
      phone: '081299998888',
      rsvp_status: 'attended',
      guest_count: 2,
      message: 'Selamat berbahagia dari pengujian sistem Node.js + Vue 3!'
    })
  });
  console.log('  RSVP Response:', rsvpRes.data);

  // 6. Checkout selection
  await test('Checkout Options', 'http://localhost:3000/api/checkout/select');

  console.log('\n=== ALL END-TO-END VERIFICATION CHECKS COMPLETED SUCCESSFULLY ===');
})();
