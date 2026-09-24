window.addEventListener("load", () => {
    const bannerImg = document.querySelector('.banner__container-img');
    const bannerSandwich = document.querySelector('.banner__sandwich');
    const bannerTitle = document.querySelector('.banner__title');
    const bannerAppointment = document.querySelector('.banner__appointment');

    if (bannerImg) bannerImg.classList.add('showBanner');
    if (bannerSandwich) bannerSandwich.classList.add('showSandwich');
    if (bannerTitle) bannerTitle.classList.add('showText');
    if (bannerAppointment) bannerAppointment.classList.add('showText');
});
