class Facets {
  constructor(facets) {
    this.facets = [];
    this.facetsContainer = facets;
    this.facetsContainer.addEventListener('click', this.handleFacetsClick.bind(this));
    this.init();
  }

  handleFacetsClick(event) {
    if (event.target.classList.contains('bms-bloglist-facet')) {
      this.toggleFacet(event.target);
    }
  }

  toggleFacet(facet) {
    if (facet.classList.contains('active')) {
      facet.classList.remove('active');
      this.facets = this.facets.filter(item => item !== facet.dataset.facet);
    } else {
      facet.classList.add('active');
      this.facets.push(facet.dataset.facet);
    }
    this.updateFacets();
  }

  updateFacets() {
    const facets = this.facets.join(',');
    const url = new URL(window.location.href);
    if (this.facets.length > 0) {
      url.searchParams.set('facets', facets);
    } else {
      url.searchParams.delete('facets');
    }
    window.location.href = url.href;
  }

  init() {
    const url = new URL(window.location.href);
    const facets = url.searchParams.get('facets');
    if (facets) {
      this.facets = facets.split(',');
      this.facets.forEach(facet => {
        const facetElement = this.facetsContainer.querySelector(`[data-facet="${facet}"]`);
        facetElement.classList.add('active');
      });
    } else {
      this.facetsContainer.querySelector('a.bms-bloglist-facet').classList.add('active');
    }
  }
}

const initFacets = facetsElement => {
  new Facets(facetsElement);
};

export { initFacets };
