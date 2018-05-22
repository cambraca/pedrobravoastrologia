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

// Load another post in "js-mode" (replaces the full page refresh)
async function handleEphemerisClick(event) {
  const href = this.getAttribute('href');
  const calendarLink = document.querySelector('#sidebar-calendar a[href=' + href.replace(/\//g, '\\/') + ']');
  const fullPostSelector = 'article.post.full';
  const post = document.querySelector(fullPostSelector);

  calendarLink.classList.add('loading');
  post.classList.add('loading');

  try {
    event.preventDefault();
    const data = await jQuery.getJSON('/js' + href);

    const parent = post.parentNode;

    parent.removeChild(post);
    const template = document.createElement('template');
    template.innerHTML = data.rendered.trim();
    parent.appendChild(template.content.querySelector(fullPostSelector));
    for (const a of parent.querySelectorAll('a[href^=\\/efemerides\\/]')) {
      a.addEventListener('click', handleEphemerisClick);
    }
    for (const a of document.querySelectorAll('#sidebar-calendar a.active'))
      a.classList.remove('active');
    calendarLink.classList.add('active');
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
