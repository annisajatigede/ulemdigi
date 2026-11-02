const { initializeApp } = require('./frontend/node_modules/firebase/app');
const { getFirestore, doc, setDoc, collection, addDoc, serverTimestamp } = require('./frontend/node_modules/firebase/firestore');

const firebaseConfig = {
  apiKey: "AIzaSyCTQDutO7mreAtAO98yeXVVTplYmpQRw08",
  authDomain: "forkoway.firebaseapp.com",
  projectId: "forkoway",
  storageBucket: "forkoway.firebasestorage.app",
  messagingSenderId: "311357809397",
  appId: "1:311357809397:web:2df5b2bc6aabc9b985dbbc",
  measurementId: "G-2V3V3YM45T"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

async function seed() {
  console.log('🚀 Seeding Firestore for Forkoway.com...');

  // 1. Settings
  console.log('1. Seeding settings...');
  await setDoc(doc(db, 'settings', 'general'), {
    site_name: 'Forkoway.com',
    site_tagline: 'Platform Undangan Digital Berbayar Modern & Elegan',
    site_description: 'Solusi undangan online terbaik untuk pernikahan, khitanan, dan acara spesial. Desain estetik, cepat, mudah digunakan, dan siap disebarkan.',
    contact_whatsapp: '6281322231433',
    contact_email: 'support@forkoway.com',
    midtrans_mode: 'sandbox',
    midtrans_client_key: '',
    midtrans_server_key: '',
    midtrans_is_production: '0',
    logo_url: '',
    favicon_url: '',
    updated_at: serverTimestamp()
  });

  // 2. Plans
  console.log('2. Seeding plans...');
  const plans = [
    {
      id: '1',
      name: 'Silver / Hemat',
      slug: 'silver',
      price: 0,
      active_days: 7,
      duration_days: 7,
      max_photos: 5,
      features: ['1 Tema Pilihan', 'Masa Aktif 7 Hari', 'Galeri hingga 5 Foto', 'Amplop Digital & RSVP', 'Google Maps Lokasi']
    },
    {
      id: '2',
      name: 'Gold / Populer',
      slug: 'gold',
      price: 89000,
      active_days: 90,
      max_photos: 15,
      features: ['Bebas Ganti Tema Kapan Saja', 'Masa Aktif 90 Hari', 'Galeri hingga 15 Foto', 'Kustom Musik Latar & Preset', 'Cerita Cinta & Hitung Mundur', 'Buku Tamu & RSVP Interaktif']
    },
    {
      id: '3',
      name: 'Platinum / Mewah',
      slug: 'platinum',
      price: 149000,
      active_days: 365,
      max_photos: 30,
      features: ['Semua Fitur Gold', 'Masa Aktif 365 Hari (1 Tahun)', 'Galeri hingga 30 Foto', 'Bebas Custom Domain Khusus', 'Prioritas Dukungan WhatsApp 24/7']
    }
  ];

  for (const p of plans) {
    await setDoc(doc(db, 'plans', p.id), {
      ...p,
      created_at: serverTimestamp()
    });
  }

  // 3. Templates (All 5 Templates)
  console.log('3. Seeding 5 templates...');
  const templates = [
    {
      id: '1',
      name: 'Floral Romance',
      category: 'wedding',
      style: 'romantic',
      blade_view: 'templates.wedding_floral',
      thumbnail_path: '/templates/thumbnails/wedding_floral.svg',
      is_active: true
    },
    {
      id: '2',
      name: 'Khitanan Ceria',
      category: 'khitan',
      style: 'playful',
      blade_view: 'templates.khitan_ceria',
      thumbnail_path: '/templates/thumbnails/khitan_ceria.svg',
      is_active: true
    },
    {
      id: '3',
      name: 'Modern Minimalist',
      category: 'general',
      style: 'modern',
      blade_view: 'templates.general_modern',
      thumbnail_path: '/templates/thumbnails/general_modern.svg',
      is_active: true
    },
    {
      id: '4',
      name: 'Islami Minimalis',
      category: 'wedding',
      style: 'islami',
      blade_view: 'templates.wedding_islami',
      thumbnail_path: '/templates/thumbnails/wedding_islami.svg',
      is_active: true
    },
    {
      id: '5',
      name: 'Rustic Autumn Floral',
      category: 'wedding',
      style: 'rustic',
      blade_view: 'templates.wedding_autumn',
      thumbnail_path: '/templates/thumbnails/wedding_autumn.png',
      is_active: true
    }
  ];

  for (const t of templates) {
    await setDoc(doc(db, 'templates', t.id), {
      ...t,
      created_at: serverTimestamp()
    });
  }

  // 4. Musics
  console.log('4. Seeding background musics...');
  const musics = [
    { id: '1', title: 'Wedding Romantic', artist: 'Instrumental Piano', file_path: '/audio/wedding_romantic.mp3', is_active: true },
    { id: '2', title: 'General Piano Bliss', artist: 'Acoustic Calm', file_path: '/audio/general_piano.mp3', is_active: true },
    { id: '3', title: 'Wedding Islami Barakah', artist: 'Gambus & Oud Soft', file_path: '/audio/wedding_islami.mp3', is_active: true },
    { id: '4', title: 'Khitan Ceria', artist: 'Happy Acoustic', file_path: '/audio/khitan_ceria.mp3', is_active: true }
  ];

  for (const m of musics) {
    await setDoc(doc(db, 'musics', m.id), {
      ...m,
      created_at: serverTimestamp()
    });
  }

  // 5. Demo Invitation (Alex & Olivia)
  console.log('5. Seeding demo invitation (alex-olivia)...');
  const demoInvRef = doc(db, 'invitations', 'alex-olivia');
  await setDoc(demoInvRef, {
    slug: 'alex-olivia',
    template_id: '5',
    plan_id: '2',
    category: 'wedding',
    status: 'published',
    published_at: serverTimestamp(),
    data: {
      opening: {
        title: 'The Wedding of',
        title_upper: 'P E R N I K A H A N',
        greeting: 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
        greeting_short: 'Kami mengundang Anda di',
        quote: 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang.',
        quote_source: 'QS. Ar-Rum: 21'
      },
      party1: {
        name: 'Alex Chastian',
        full_name: 'Alex Chastian, S.T',
        role_label: 'Mempelai Pria',
        child_order: 'Putra pertama dari',
        parents: 'Bpk. Robert Chastian & Ibu Sarah Chastian',
        instagram: '@alexchastian',
        photo_path: '/storage/invitations/1/d6tta341YkW9XCLdtl9CBamKnvrg665iIWw1jAuj.jpg'
      },
      party2: {
        name: 'Olivia Wilson',
        full_name: 'Olivia Wilson, S.Ked',
        role_label: 'Mempelai Wanita',
        child_order: 'Putri kedua dari',
        parents: 'Bpk. David Wilson & Ibu Emily Wilson',
        instagram: '@oliviawilson',
        photo_path: '/storage/invitations/1/5sAe17gwxvqTyVGhTwVTw70wN98rE9dG025DKbci.jpg'
      },
      theme_color: '#b86835',
      music_path: '/audio/wedding_romantic.mp3',
      music_url: '/audio/wedding_romantic.mp3',
      story: [
        { year: '2021', title: 'Pertama Kali Bertemu', description: 'Pertemuan hangat di kampus berujung pada percakapan indah.' },
        { year: '2023', title: 'Komitmen Bersama', description: 'Melangkah ke jenjang yang lebih serius dengan restu keluarga.' },
        { year: '2026', title: 'Menuju Pelaminan', description: 'Mengikrarkan janji suci pernikahan sehidup semati.' }
      ],
      gallery: [
        { url: '/storage/invitations/1/d6tta341YkW9XCLdtl9CBamKnvrg665iIWw1jAuj.jpg' },
        { url: '/storage/invitations/1/5sAe17gwxvqTyVGhTwVTw70wN98rE9dG025DKbci.jpg' }
      ],
      gifts: [
        { bank_name: 'BCA', account_number: '1234567890', account_holder: 'Alex Chastian' },
        { bank_name: 'Mandiri', account_number: '9876543210123', account_holder: 'Olivia Wilson' }
      ],
      closing: {
        text: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kami.',
        signature: 'Alex & Olivia Beserta Keluarga Besar'
      }
    },
    created_at: serverTimestamp(),
    updated_at: serverTimestamp()
  });

  // Events subcollection for alex-olivia
  const eventDate = new Date();
  eventDate.setDate(eventDate.getDate() + 30);
  eventDate.setHours(10, 0, 0, 0);

  await setDoc(doc(db, 'invitations', 'alex-olivia', 'events', 'main-event'), {
    title: 'Akad Nikah & Resepsi',
    start_at: eventDate.toISOString(),
    venue_name: 'Grand Ballroom Hotel Arya',
    address: '123 Anywhere St, Any City, ST 1234',
    maps_url: 'https://maps.google.com',
    created_at: serverTimestamp()
  });

  // Sample wishes
  await addDoc(collection(db, 'invitations', 'alex-olivia', 'wishes'), {
    name: 'Budi Santoso & Keluarga',
    message: 'Selamat menempuh hidup baru Alex & Olivia! Semoga senantiasa sakinah, mawaddah, warahmah.',
    rsvp_status: 'attended',
    guest_count: 2,
    created_at: serverTimestamp()
  });

  console.log('✅ Firestore successfully seeded!');
  process.exit(0);
}

seed().catch(err => {
  console.error('❌ Seeding error:', err);
  process.exit(1);
});
