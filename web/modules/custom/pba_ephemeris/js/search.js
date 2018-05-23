(() => {

  function handleSearchResultClick(event) {
    document.getElementById('search').value = '';
    handleSearch();
    handleEphemerisClick.call(this, event);
  }

  let searchTimeout = null;
  function handleSearch() {
    if (searchTimeout)
      clearTimeout(searchTimeout);

    searchTimeout = setTimeout(doSearch, 150);
  }

  function doSearch() {
    const query = document.getElementById('search').value.trim();

    if (query === '') {
      document.getElementById('block-searchbox').classList.remove('is-searching');
      hideSearchResults();
      return;
    }

    document.getElementById('block-searchbox').classList.add('is-searching');

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
  const hideSearchResults = () => {
    searchResults.classList.remove('show');
  };
  const clearSearchResults = () => {
    while (searchResults.childNodes.length) {
      searchResults.removeChild(searchResults.childNodes[0]);
    }
  };

  search.addEventListener('keyup', handleSearch);
  search.addEventListener('click', () => setTimeout(handleSearch));

  search.classList.add('is-initialized');

  /**
   * Masonry code adapted from
   * https://medium.com/@andybarefoot/a-masonry-style-layout-using-css-grid-8c663d355ebb
   */

  function resizeGridItem(item) {
    const rowHeight = 5;
    const rowGap = 5;
    const rowSpan = Math.ceil((item.querySelector('img').getBoundingClientRect().height + rowGap) / (rowHeight + rowGap));
    item.style.gridRowEnd = "span " + rowSpan;
  }

  function resizeAllGridItems() {
    const allItems = document.getElementById('search-results').childNodes;
    for (let x = 0; x < allItems.length; x++) {
      resizeGridItem(allItems[x]);
    }
  }

  let resizeTimeout = null;
  function resizeInstance(instance) {
    const item = instance.elements[0];
    resizeGridItem(item);

    if (resizeTimeout)
      clearTimeout(resizeTimeout);

    resizeTimeout = setTimeout(resizeAllGridItems, 150);

  }

  window.addEventListener("resize", resizeAllGridItems);

})();
