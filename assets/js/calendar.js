select: function(info) {
    const date = info.startStr; // format ISO: 2025-08-28T10:00:00
    window.location.href = `/administrateur/creer-entretien?date=${date}`;
  }
  