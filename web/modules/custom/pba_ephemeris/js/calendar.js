// The code on this file assumes its script tag appears after the sidebar
// calendar.

// Make sure we show the current day on the calendar sidebar
const sidebar = document.getElementById('sidebar');
sidebar.scrollTop = sidebar.scrollHeight;
const sidebarCalendar = document.getElementById('sidebar-calendar');
if (sidebarCalendar) {
  const active = sidebarCalendar.getElementsByClassName('active');
  if (active.length > 0) {
    active[0].scrollIntoView({behavior: "instant"});
  }
}

const postCache = [];
const POST_CACHE_LIMIT = 50;
const imageCache = [];
const IMAGE_CACHE_LIMIT = 50;

function loadPost(data) {
  const post = document.querySelector('article.post.full');
  const parent = post.parentNode;
  const calendarLink = document.querySelector('#sidebar-calendar a[href=' + data.url.replace(/\//g, '\\/') + ']');

  parent.removeChild(post);
  const template = document.createElement('template');
  template.innerHTML = data.rendered.trim();
  parent.appendChild(template.content.querySelector('article.post.full'));

  for (const a of parent.querySelectorAll('a[href^=\\/efemerides\\/]')) {
    a.addEventListener('click', handleEphemerisClick);
  }

  for (const a of document.querySelectorAll('#sidebar-calendar a.active'))
    a.classList.remove('active');
  calendarLink.classList.add('active');

  document.title = data.title + ' | Pedro Bravo Astrología';
}

// Load another post in "js-mode" (replaces the full page refresh)
async function handleEphemerisClick(event) {
  const url = this.getAttribute('href');
  const calendarLink = document.querySelector('#sidebar-calendar a[href=' + url.replace(/\//g, '\\/') + ']');
  const post = document.querySelector('article.post.full');

  calendarLink.classList.add('loading');
  post.classList.add('loading');

  try {
    event.preventDefault();
    let data = getFromPostCache(url);
    if (!data)
      data = await jQuery.getJSON('/js' + url);
    loadPost(data);
    window.history.pushState(data, data.title, data.url);
    setTimeout(preloadAdjacentPosts);
  }
  catch (e) {
    console.error(e);
  }

  calendarLink.classList.remove('loading');
}

// Apply "js-mode" to post links.
for (const a of document.querySelectorAll('a[href^=\\/efemerides\\/]')) {
  a.addEventListener('click', handleEphemerisClick);
}

window.addEventListener('popstate', function(event) {
  loadPost(event.state);
});

window.history.replaceState({
  'url': window.location.pathname,
  'title': document.title.substr(0,10).trim(),
  'rendered': document.querySelector('article.post.full').outerHTML,
}, document.title);

async function preloadAdjacentPost(url) {
  const data = await jQuery.getJSON('/js' + url);
  postCache.push({url: url, data: data});
  if (postCache.length > POST_CACHE_LIMIT)
    postCache.shift();

  // Preload images
  const template = document.createElement('template');
  template.innerHTML = data.rendered.trim();
  for (const img of template.content.querySelectorAll('img')) {
    const imageUrl = img.getAttribute('src');
    if (!getFromImageCache(imageUrl)) {
      const image = new Image();
      image.src = imageUrl;
      imageCache.push({url: imageUrl, image: image});
      if (imageCache.length > IMAGE_CACHE_LIMIT)
        imageCache.shift();
    }
  }
}

function getFromPostCache(url) {
  for (const item of postCache) {
    if (item.url === url)
      return item.data;
  }
}

function getFromImageCache(url) {
  for (const item of imageCache) {
    if (item.url === url)
      return item.image;
  }
}

function preloadAdjacentPosts() {
  const prev = document.querySelector('article.post.full .prev article.post a');
  if (prev && !getFromPostCache(prev.getAttribute('href')))
    preloadAdjacentPost(prev.getAttribute('href'));
  const next = document.querySelector('article.post.full .next article.post a');
  if (next && !getFromPostCache(next.getAttribute('href')))
    preloadAdjacentPost(next.getAttribute('href'));
}

setTimeout(preloadAdjacentPosts);

document.addEventListener('keyup', event => {
  switch (event.keyCode) {
    case 37: // Left arrow key
      const prev = document.querySelector('article.post.full .prev article.post a');
      if (prev)
        prev.click();
      break;
    case 39: // Right arrow key
      const next = document.querySelector('article.post.full .next article.post a');
      if (next)
        next.click();
      break;
  }
});
