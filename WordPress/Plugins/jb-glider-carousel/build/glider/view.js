/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************!*\
  !*** ./src/glider/view.js ***!
  \****************************/
/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/* eslint-disable no-console */
console.log('Hello World! (from jeremiahbratton-jb-glider-carousel block)');
/* eslint-enable no-console */
const newGliders = document.querySelectorAll('.wp-block-jeremiahbratton-jb-glider-carousel .jb-glider-container:not(.glider)');
newGliders.forEach(newGlider => {
  /**
   * To keep controls attached to gliders while also not performing a bunch of ID saving nonsense on 
   * the block editor side, we are going to generate a random ID to use for this instance and then 
   * attach it to individual control elements.
   */
  const newGliderID = 'jb-glider-' + Math.floor(Math.random() * 1000);
  if (newGlider.dataset.dots) {
    const newGliderDots = newGlider.parentElement.querySelector('.dots');
    newGliderDots.dataset.parent = newGliderID;
  }
  if (newGlider.dataset.arrows) {
    const newGliderPrev = newGlider.parentElement.querySelector('.glider-prev');
    newGliderPrev.dataset.parent = newGliderID;
    const newGliderNext = newGlider.parentElement.querySelector('.glider-next');
    newGliderNext.dataset.parent = newGliderID;
  }
  const gliderConfig = {
    slidesToShow: 1,
    slidesToScroll: 1,
    rewind: newGlider.dataset.rewind,
    scrollLock: newGlider.dataset.scrolllock,
    draggable: newGlider.dataset.draggable,
    responsive: [{
      breakpoint: 768,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: newGlider.dataset.slidestoshow,
        slidesToScroll: newGlider.dataset.slidestoscroll
      }
    }]
  };
  if (newGlider.dataset.arrows === 'true') {
    gliderConfig.arrows = {
      prev: '.glider-prev[data-parent="' + newGliderID + '"]',
      next: '.glider-next[data-parent="' + newGliderID + '"]'
    };
  }
  if (newGlider.dataset.dots === 'true') {
    gliderConfig.dots = '.dots[data-parent="' + newGliderID + '"]';
  }
  new Glider(newGlider, gliderConfig);
});
/******/ })()
;
//# sourceMappingURL=view.js.map