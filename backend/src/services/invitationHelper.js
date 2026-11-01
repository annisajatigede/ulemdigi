const crypto = require('crypto');

function getSafeData(raw) {
  const data = typeof raw === 'string' ? JSON.parse(raw) : (raw || {});

  const defaults = {
    theme_color: '#8b5cf6',
    music_path: null,
    music_title: null,
    music_source: 'preset',
    opening: {
      title: 'The Wedding of',
      quote: 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri...',
      quote_source: 'QS. Ar-Rum: 21',
      greeting: 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
    },
    party1: {
      name: 'Fulanah',
      full_name: 'Siti Fulanah, S.Kom',
      role_label: 'Mempelai Wanita',
      child_order: 'Putri pertama dari',
      parents: 'Bpk. Ahmad & Ibu Fatimah',
      instagram: '@fulanah',
      photo_path: null,
    },
    party2: {
      name: 'Fulan',
      full_name: 'Muhammad Fulan, S.T',
      role_label: 'Mempelai Pria',
      child_order: 'Putra kedua dari',
      parents: 'Bpk. Hasan & Ibu Aminah',
      instagram: '@fulan',
      photo_path: null,
    },
    gallery: [],
    story: [],
    gifts: [],
    rsvp_enabled: true,
    wishes_enabled: true,
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

function daysRemaining(expiresAt) {
  if (!expiresAt) return 0;
  const now = new Date().getTime();
  const exp = new Date(expiresAt).getTime();
  if (now > exp) return 0;
  return Math.ceil((exp - now) / (1000 * 60 * 60 * 24));
}

function isExpired(status, expiresAt) {
  if (status === 'expired') return true;
  if (expiresAt && new Date().getTime() > new Date(expiresAt).getTime()) {
    return true;
  }
  return false;
}

function isPublished(status, expiresAt) {
  return status === 'published' && !isExpired(status, expiresAt);
}

function generateRandomSlug(category) {
  const prefix = category === 'khitan' ? 'khitan' : (category === 'general' ? 'acara' : 'momen');
  const rand = crypto.randomBytes(4).toString('hex').toLowerCase().slice(0, 7);
  return `${prefix}-${rand}`;
}

function buildInitialInvitationData(category, userName = 'Ahmad') {
  if (category === 'khitan') {
    return {
      theme_color: '#0ea5e9',
      music_path: null,
      music_source: 'preset',
      music_title: null,
      opening: {
        title: 'Walimatul Khitan',
        quote: 'Dan semoga anak kami menjadi anak yang sholeh, berbakti kepada orang tua, agama, nusa dan bangsa.',
        quote_source: 'Doa Syukuran Khitan',
        greeting: 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
      },
      party1: {
        name: 'Muhammad Rayyan',
        full_name: 'Muhammad Rayyan Al-Ghifari',
        role_label: 'Anak yang Dikhitan',
        child_order: 'Putra tercinta dari',
        parents: `${userName} & Ibu Fatimah`,
        instagram: '@rayyan.family',
        photo_path: null,
      },
      party2: {
        name: 'Keluarga Besar',
        full_name: `Keluarga Bpk. ${userName}`,
        role_label: 'Orang Tua / Wali',
        child_order: '',
        parents: 'Kediaman Keluarga Besar',
        instagram: '',
        photo_path: null,
      },
      gallery: [],
      story: [
        {
          year: 'Momen Syukuran',
          title: 'Langkah Menuju Kedewasaan',
          description: 'Syukuran khitanan putra kami tersayang sebagai wujud ketaatan syariat dan rasa syukur kepada Allah SWT.',
        },
      ],
      gifts: [
        {
          bank_name: 'BCA',
          account_number: '1234567890',
          account_holder: userName,
        },
      ],
      rsvp_enabled: true,
      wishes_enabled: true,
      closing: {
        text: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami sekeluarga apabila Bapak/Ibu/Saudara/i berkenan hadir serta memberikan doa restu.',
        signature: `Keluarga Besar Bpk. ${userName}`,
      },
    };
  }

  if (category === 'general') {
    return {
      theme_color: '#10b981',
      music_path: null,
      music_source: 'preset',
      music_title: null,
      opening: {
        title: 'Special Celebration',
        quote: 'Kebahagiaan terasa sempurna ketika dapat berbagi momen istimewa bersama orang-orang tercinta.',
        quote_source: '',
        greeting: 'Salam Sejahtera / Assalamu’alaikum',
      },
      party1: {
        name: 'Nama Tuan Rumah / Acara',
        full_name: 'Penyelenggara Acara',
        role_label: 'Penyelenggara',
        child_order: '',
        parents: '',
        instagram: '@acara.kita',
        photo_path: null,
      },
      party2: {
        name: '',
        full_name: '',
        role_label: '',
        child_order: '',
        parents: '',
        instagram: '',
        photo_path: null,
      },
      gallery: [],
      story: [],
      gifts: [],
      rsvp_enabled: true,
      wishes_enabled: true,
      closing: {
        text: 'Kehadiran dan doa restu Anda merupakan suatu kehormatan yang tak ternilai bagi kami.',
        signature: 'Panitia / Penyelenggara',
      },
    };
  }

  // Default Wedding
  return {
    theme_color: '#e11d48',
    music_path: null,
    music_source: 'preset',
    music_title: null,
    opening: {
      title: 'The Wedding of',
      quote: 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya...',
      quote_source: 'QS. Ar-Rum: 21',
      greeting: 'Assalamu’alaikum Warahmatullahi Wabarakatuh',
    },
    party1: {
      name: 'Fulanah',
      full_name: 'Siti Fulanah, S.Kom',
      role_label: 'Mempelai Wanita',
      child_order: 'Putri pertama dari',
      parents: 'Bpk. Ahmad & Ibu Fatimah',
      instagram: '@fulanah',
      photo_path: null,
    },
    party2: {
      name: 'Fulan',
      full_name: 'Muhammad Fulan, S.T',
      role_label: 'Mempelai Pria',
      child_order: 'Putra kedua dari',
      parents: 'Bpk. Hasan & Ibu Aminah',
      instagram: '@fulan',
      photo_path: null,
    },
    gallery: [],
    story: [
      {
        year: '2023',
        title: 'Pertama Bertemu',
        description: 'Awal mula perkenalan yang hangat di bangku kuliah.',
      },
      {
        year: '2025',
        title: 'Lamaran & Komitmen',
        description: 'Menyatukan niat tulus kedua keluarga besar.',
      },
    ],
    gifts: [
      {
        bank_name: 'BCA',
        account_number: '1234567890',
        account_holder: 'Fulanah / Fulan',
      },
    ],
    rsvp_enabled: true,
    wishes_enabled: true,
    closing: {
      text: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.',
      signature: 'Keluarga Besar Kami',
    },
  };
}

module.exports = {
  getSafeData,
  daysRemaining,
  isExpired,
  isPublished,
  generateRandomSlug,
  buildInitialInvitationData,
};
