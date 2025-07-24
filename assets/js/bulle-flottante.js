
  const mainBubbleBtn = document.getElementById("mainBubbleBtn");
  const actionButtons = document.getElementById("actionButtons");
  
  // Toggle des actions au clic
  mainBubbleBtn.addEventListener("click", () => {
    actionButtons.classList.toggle("show");
  });
  
  // Masquer les actions au scroll
  let lastScrollTop = 0;
  
  window.addEventListener("scroll", () => {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  
    if (scrollTop > lastScrollTop) {
      // Scroll vers le bas
      actionButtons.classList.remove("show");
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Support mobile
  });
  



  document.querySelector('.call-vcf')?.addEventListener('click', () => {
    window.location.href = 'tel:+0613752773';
  
    setTimeout(() => {
      window.open('/assets/pages/contact.vcf.php', '_blank');
  
      // 👁 Affiche un message temporaire
      const notice = document.getElementById('vcfNotice');
      if (notice) {
        notice.style.display = 'block';
        setTimeout(() => {
          notice.style.display = 'none';
        }, 3000);
      }
    }, 500);
  });
  