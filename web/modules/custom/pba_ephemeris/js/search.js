(() => {

  function handleSearchResultClick(event) {
    document.getElementById('search').value = '';
    handleSearch();
    handleEphemerisClick.call(this, event);
  }

  function handleSearch() {
    const query = document.getElementById('search').value.trim();

    if (query === '') {
      hideSearchResults();
      return;
    }

    algoliaIndex.search(query, (err, content) => {
      if (err) {
        hideSearchResults();
        console.error('Error while searching', err);
        return;
      }

      clearSearchResults();

      if (content.hits.length === 0) {
        const noResults = document.createElement('div');
        noResults.innerHTML = 'No se encontraron resultados.';
        searchResults.appendChild(noResults);
      }

      content.hits.map(hit => {
        const a = document.createElement('a');
        a.setAttribute('href', hit.url);
        a.innerHTML = `<img src="${hit.image}">`;
        a.addEventListener('click', handleSearchResultClick);
        searchResults.appendChild(a);
        imagesLoaded(a, resizeInstance);
      });

      showSearchResults();
    })
  }

  const algoliaClient = algoliasearch(drupalSettings.algoliaAppId, drupalSettings.algoliaSearchApiKey);
  const algoliaIndex = algoliaClient.initIndex('posts');

  const search = document.getElementById('search');
  const searchResults = document.createElement('div');
  searchResults.id = 'search-results';
  document.getElementById('main').insertBefore(searchResults, document.getElementById('main-content'));

  const showSearchResults = () => {
    searchResults.classList.add('show');
    resizeAllGridItems();
  };
  const hideSearchResults = () => searchResults.classList.remove('show');
  const clearSearchResults = () => {
    while (searchResults.childNodes.length) {
      searchResults.removeChild(searchResults.childNodes[0]);
    }
  };

  search.addEventListener('keyup', handleSearch);

  search.classList.add('is-initialized');

  /**
   * Masonry code adapted from
   * https://medium.com/@andybarefoot/a-masonry-style-layout-using-css-grid-8c663d355ebb
   */

  function resizeGridItem(item) {
    const rowHeight = 1;
    const rowGap = 30;
    const rowSpan = Math.ceil((item.querySelector('img').getBoundingClientRect().height + rowGap) / (rowHeight + rowGap));
    item.style.gridRowEnd = "span " + rowSpan;
  }

  function resizeAllGridItems() {
    const allItems = document.getElementById('search-results').childNodes;
    for (let x = 0; x < allItems.length; x++) {
      resizeGridItem(allItems[x]);
    }
  }

  function resizeInstance(instance) {
    const item = instance.elements[0];
    resizeGridItem(item);
  }

  window.addEventListener("resize", resizeAllGridItems);

})();
