import './bootstrap.js';

// Back-to-top: safe + perf-friendly
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.back-to-top');
  if (!btn) return;

  // état initial
  btn.style.display = window.scrollY > 300 ? 'flex' : 'none';

  // écouteur scroll (passive)
  window.addEventListener(
    'scroll',
    () => {
      // micro-throttle via requestAnimationFrame
      if (btn.__raf__) return;
      btn.__raf__ = requestAnimationFrame(() => {
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
        btn.__raf__ = null;
      });
    },
    { passive: true }
  );
});



/*--- JS : intercepter, envoyer en fetch, afficher toast--- */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('footer-contact');
  if (!form) return;

  const btn         = document.getElementById('footer-contact-submit');
  const toastOkEl   = document.getElementById('contactToast');
  const toastErrEl  = document.getElementById('contactToastError');
  const toastOkMsg  = document.getElementById('contactToastMessage');
  const toastErrMsg = document.getElementById('contactToastErrorMessage');

  const Toast = window.bootstrap?.Toast;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // 🔹 Vérif front : message obligatoire
    const messageField = form.querySelector('textarea[name="message"]');
    if (messageField && !messageField.value.trim()) {
      if (Toast && toastErrEl) {
        toastErrMsg.textContent = 'Merci de préciser votre message avant d’envoyer.';
        new Toast(toastErrEl, { delay: 4000 }).show();
      }
      messageField.focus();
      return;
    }

    btn.disabled = true;
    btn.innerHTML = 'Envoi…';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok || !data.ok) {
        throw new Error(data.message || 'Erreur lors de l’envoi');
      }

      // ✅ Succès
      if (Toast && toastOkEl) {
        toastOkMsg.textContent = data.message || 'Message envoyé ✔️';
        new Toast(toastOkEl, { delay: 3500 }).show();
      }
      form.reset();

    } catch (err) {
      if (Toast && toastErrEl) {
        toastErrMsg.textContent = err.message || 'Oups, un problème est survenu.';
        new Toast(toastErrEl, { delay: 4500 }).show();
      }
    } finally {
      btn.disabled = false;
      btn.innerHTML = 'Envoyer';
    }
  });
});


// -------- Planning : modale cours --------
(function () {

  function attachCourseModal() {
    const CAL = document.querySelector('.week-calendar');
    if (!CAL) return;

    // ✅ Empêche de ré-attacher plusieurs fois (Turbo, reload partiel, etc.)
    if (CAL.dataset.modalBound === '1') {
      return;
    }
    CAL.dataset.modalBound = '1';

   const MAP = {
    'STEP': {
      titre: 'Step',
      desc: "Chorégraphie cardio, fun et accessible.",
      img: '/images/step.jpg'
    },
    'STEP DEBUTANT': {
      titre: 'Step Débutant',
      desc: "Apprentissage des bases du step en douceur, parfait pour débuter.",
      img: '/images/step.jpg'
    },
    'CARDIOLAND': {
      titre: 'Cardioland',
      desc: "Séance cardio ludique pour brûler des calories et booster l’endurance.",
      img: '/images/cardioland01.jpg'
    },
    'HIIT': {
      titre: 'HIIT',
      desc: "Intervalles haute intensité pour des résultats maximum en peu de temps.",
      img: '/images/hiit.jpg'
    },
    'FUNCTIONAL TRAINING': {
      titre: 'Functional Training',
      desc: "Renforcement complet, mobilité et cardio avec des mouvements fonctionnels.",
      img: '/images/functional.jpg'
    },
    'HYBRID TRAINING': {
      titre: 'Hybrid Training',
      desc: "Mix explosif de course, renfo et cardio. Idéal pour se dépasser.",
      img: '/images/hybrid.jpg'
    },
    'SOUPLESSE': {
      titre: 'Souplesse Yoga Pilates',
      desc: "Mobilité, étirements et gainage profond pour un corps plus libre.",
      img: '/images/souplesse.jpg'
    },
    'SOUPLESSE YOGA PILATE': {
      titre: 'Souplesse Yoga Pilates',
      desc: "Mobilité, étirements et gainage profond pour un corps plus libre.",
      img: '/images/souplesse.jpg'
    },
    'GYM SENIOR': {
      titre: 'Gym Sénior',
      desc: "Séance douce et sécurisée pour entretenir mobilité, équilibre et tonus.",
      img: '/images/gym.jpg'
    },
    'MARCHE NORDIQUE': {
      titre: 'Marche Nordique / Running',
      desc: "Cardio en extérieur, technique et convivialité.",
      img: '/images/running.jpg'
    },
    'RUNNING': {
      titre: 'Marche Nordique / Running',
      desc: "Cardio en extérieur, technique et convivialité.",
      img: '/images/running.jpg'
    },
    'STADE': {
      titre: 'Stade',
      desc: "Travail technique, vitesse et agilité en plein air.",
      img: '/images/stade.jpg'
    },
    "MOVE N'DANCE": {
      titre: "Move N'Dance",
      desc: "Chorés simples, ambiance fun, parfait pour se lâcher.",
      img: '/images/move.jpg'
    },
    'FIT DANCE': {
      titre: 'Fit Dance',
      desc: "On construit une choré au fil du cours, dynamique et ludique.",
      img: '/images/fitdance.jpg'
    },
    'HEELS': {
      titre: 'Heels',
      desc: "Danse sur talons, assurance, sensualité et style.",
      img: '/images/heels.jpg'
    },
    'JUMP': {
      titre: 'Jump',
      desc: "Mini-trampoline pour un cardio fun, sans impact articulaire.",
      img: '/images/cardioland.jpg'
    },
    'SANGLES': {
      titre: 'Sangles',
      desc: "TRX : renfo global, stabilité et gainage.",
      img: '/images/sangles.jpg'
    },
    'FLYING POLE': {
      titre: 'Flying Pole',
      desc: "Barre de Pole suspendue : force, grâce et défis aériens.",
      img: '/images/flying.jpg'
    },
    'YOGA': {
      titre: 'Yoga',
      desc: "Respiration, mobilité, recentrage : un moment pour soi.",
      img: '/images/souplesse.jpg'
    },
    'ABDOS FLASH': {
      titre: 'Abdos Flash',
      desc: "Ciblage express de la sangle abdominale en 30 minutes.",
      img: '/images/abdos.jpg' 
    }
  };


    const DAY_FR = {
      mon:'lundi', tue:'mardi', wed:'mercredi',
      thu:'jeudi', fri:'vendredi', sat:'samedi', sun:'dimanche'
    };

    function normTitle(s){
      return (s || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g,'')
        .toUpperCase()
        .trim();
    }

    function openModalFromEvent(el){
      const col    = el.closest('.day-col');
      const dayKey = col?.dataset.day || 'mon';
      const dayLbl = DAY_FR[dayKey] || dayKey;
      const baseSeanceUrl = CAL.dataset.seanceUrl || '/seance-essai';

      const time     = el.querySelector('.time')?.textContent?.trim() || '';
      const rawTitle = el.querySelector('.title')?.textContent?.trim() || '';
      const key      = normTitle(rawTitle);

      const meta = MAP[key] || {
        titre: rawTitle || 'Cours',
        desc: "Pour ce cours, la description détaillée sera prochainement ajoutée.",
        img:  '/images/functional.jpg'
      };

      const $title = document.getElementById('cm-title');
      const $desc  = document.getElementById('cm-desc');
      const $img   = document.getElementById('cm-img');
      const $when  = document.getElementById('cm-when');
      const $cta   = document.getElementById('cm-cta');
      const $modal = document.getElementById('courseModal');

      if (!$title || !$desc || !$img || !$when || !$cta || !$modal) return;

      $title.textContent = meta.titre;
      $desc.textContent  = meta.desc;
      $img.style.backgroundImage = `url('${meta.img}')`;
      $when.textContent  = `${dayLbl} • ${time}`;

      const m = time.match(/(\d{2}:\d{2})\s*[\-–]\s*(\d{2}:\d{2})/);
      const start = m ? m[1] : '';
      const end   = m ? m[2] : '';

      const url = new URL(baseSeanceUrl, window.location.origin);
      url.searchParams.set('title', meta.titre);
      if (start) url.searchParams.set('start', start);
      if (end)   url.searchParams.set('end', end);
      
       // On passe aussi le jour (mon, tue, …) pour cibler le bon jour de la semaine
      url.searchParams.set('day', dayKey);
      //anchor + scroll auto vers le formulaire pré rempli
      url.hash = 'reserver';

      $cta.href = url.toString();

      const modal = bootstrap.Modal.getOrCreateInstance($modal);
      modal.show();
    }

    // Délégation clic
    CAL.addEventListener('click', (ev) => {
      const card = ev.target.closest('.event');
      if (!card) return;
      openModalFromEvent(card);
    });

    // Clavier
    CAL.addEventListener('keydown', (ev) => {
      if (ev.key !== 'Enter' && ev.key !== ' ') return;
      const card = ev.target.closest('.event');
      if (!card) return;
      ev.preventDefault();
      openModalFromEvent(card);
    });

    // Focusable
    CAL.querySelectorAll('.event').forEach(e => {
      e.setAttribute('tabindex','0');
    });
  }

  // Init classique
  if (document.readyState !== 'loading') {
    attachCourseModal();
  } else {
    document.addEventListener('DOMContentLoaded', attachCourseModal);
  }

  // Turbo (si utilisé)
  window.addEventListener?.('turbo:load', attachCourseModal);
  window.addEventListener?.('turbo:render', attachCourseModal);

})();











