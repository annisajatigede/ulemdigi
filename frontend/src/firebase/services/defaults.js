export const defaultTemplates = [
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

export const defaultPlans = [
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

export const defaultMusics = [
  { id: '1', title: 'Janji Suci Kita (Acoustic)', artist: 'Forkoway Special Wedding', file_path: '/audio/janji_suci_kita_01.mp3', audio_url: '/audio/janji_suci_kita_01.mp3', category: 'wedding', is_active: true },
  { id: '2', title: 'Janji Suci Kita (Romantic Duet)', artist: 'Forkoway Special Wedding', file_path: '/audio/janji_suci_kita_02.mp3', audio_url: '/audio/janji_suci_kita_02.mp3', category: 'wedding', is_active: true },
  { id: '3', title: 'Mengikat Janji Suci (Ballad Piano)', artist: 'Forkoway Special Wedding', file_path: '/audio/mengikat_janji_suci_01.mp3', audio_url: '/audio/mengikat_janji_suci_01.mp3', category: 'wedding', is_active: true },
  { id: '4', title: 'Mengikat Janji Suci (Orchestral Grand)', artist: 'Forkoway Special Wedding', file_path: '/audio/mengikat_janji_suci_02.mp3', audio_url: '/audio/mengikat_janji_suci_02.mp3', category: 'wedding', is_active: true }
];

export const defaultSettings = {
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
  favicon_url: ''
};

export const demoInvitation = {
  id: 'alex-olivia',
  slug: 'alex-olivia',
  title: 'Alex & Olivia Wedding',
  template_id: '5',
  plan_id: '2',
  category: 'wedding',
  status: 'published',
  views_count: 142,
  user_id: 'demo-user',
  templates: defaultTemplates[4], // Rustic Autumn Floral
  events: [
    {
      id: 'main-event',
      title: 'Akad Nikah & Resepsi',
      start_at: '2026-09-17T10:00:00.000Z',
      end_at: '2026-09-17T14:00:00.000Z',
      venue_name: 'Grand Ballroom Hotel Arya',
      address: '123 Anywhere St, Any City, ST 1234',
      maps_url: 'https://maps.google.com'
    }
  ],
  wishes: [
    {
      id: 'wish-1',
      name: 'Budi Santoso & Keluarga',
      message: 'Selamat menempuh hidup baru Alex & Olivia! Semoga senantiasa sakinah, mawaddah, warahmah.',
      rsvp_status: 'attended',
      guest_count: 2,
      created_at: new Date().toISOString()
    },
    {
      id: 'wish-2',
      name: 'Rina & Dimas',
      message: 'Happy wedding brother! Lancar sampai hari H dan bahagia selalu selamanya.',
      rsvp_status: 'attended',
      guest_count: 2,
      created_at: new Date().toISOString()
    }
  ],
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
    music_path: '/audio/janji_suci_kita_01.mp3',
    music_url: '/audio/janji_suci_kita_01.mp3',
    music_title: 'Janji Suci Kita (Acoustic)',
    music_source: 'preset',
    rsvp_enabled: true,
    wishes_enabled: true,
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
  }
};

export function getSafeData(raw) {
  const data = typeof raw === 'string' ? JSON.parse(raw) : (raw || {});

  const defaults = {
    theme_color: '#8b5cf6',
    music_path: null,
    music_title: null,
    music_source: 'preset',
    rsvp_enabled: true,
    wishes_enabled: true,
    opening: {
      title: 'The Wedding of',
      quote: 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri...',
      quote_source: 'QS. Ar-Rum: 21',
      greeting: 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
    },
    party1: {
      name: 'Fulan',
      full_name: 'Muhammad Fulan, S.T',
      role_label: 'Mempelai Pria',
      child_order: 'Putra pertama dari',
      parents: 'Bpk. Hasan & Ibu Aminah',
      instagram: '@fulan',
      photo_path: null,
    },
    party2: {
      name: 'Fulanah',
      full_name: 'Siti Fulanah, S.Kom',
      role_label: 'Mempelai Wanita',
      child_order: 'Putri kedua dari',
      parents: 'Bpk. Ahmad & Ibu Fatimah',
      instagram: '@fulanah',
      photo_path: null,
    },
    gallery: [],
    story: [],
    gifts: [],
    closing: {
      text: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.',
      signature: 'Keluarga Besar Kami',
    },
  };

  const merged = {
    ...defaults,
    ...data,
    opening: { ...defaults.opening, ...(data.opening || {}) },
    party1: { ...defaults.party1, ...(data.party1 || {}) },
    party2: { ...defaults.party2, ...(data.party2 || {}) },
    closing: { ...defaults.closing, ...(data.closing || {}) },
    gallery: Array.isArray(data.gallery) ? data.gallery : defaults.gallery,
    story: Array.isArray(data.story) ? data.story : defaults.story,
    gifts: Array.isArray(data.gifts) ? data.gifts : defaults.gifts,
  };

  if (merged.music_path) {
    if (merged.music_path.startsWith('http://') || merged.music_path.startsWith('https://')) {
      merged.music_url = merged.music_path;
    } else {
      merged.music_url = merged.music_path.startsWith('/') ? merged.music_path : '/' + merged.music_path;
    }
  } else {
    merged.music_url = null;
  }

  return merged;
}
