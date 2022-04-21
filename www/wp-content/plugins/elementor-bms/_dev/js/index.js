/* eslint-disable no-undef */

// import styles
import '../css/style.scss';

import { initSlider } from './slider';
import { initFacets } from './facets';

function render(wrapper) {
  if (wrapper.querySelector('.bms-bloglist-carousel') !== null) {
    initSlider(`.elementor-element-${wrapper.dataset.id} .bms-bloglist-carousel`);
  }
  if (wrapper.querySelector('.bms-bloglist-facets') !== null) {
    initFacets(wrapper.querySelector('.bms-bloglist-facets'));
  }
}

jQuery(window).on('elementor/frontend/init', () => {
  elementorFrontend.hooks.addAction('frontend/element_ready/bms-posts-list.default', el => {
    render(el[0]);
  });
});
