1	// Training & Placement Portal — core front-end behaviors
2	
3	document.addEventListener('DOMContentLoaded', () => {
4	  initTheme();
5	  initSidebarToggle();
6	  initFadeInOnScroll();
7	});
8	
9	/* ---------------- Dark / Light mode ---------------- */
10	function initTheme() {
11	  const root = document.documentElement;
12	  const saved = localStorage.getItem('tpp-theme') || 'light';
13	  root.setAttribute('data-theme', saved);
14	
15	  document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
16	    btn.addEventListener('click', () => {
17	      const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
18	      root.setAttribute('data-theme', next);
19	      localStorage.setItem('tpp-theme', next);
20	    });
21	  });
22	}
23	
24	/* ---------------- Sidebar (mobile) ---------------- */
25	function initSidebarToggle() {
26	  const sidebar = document.querySelector('.sidebar');
27	  document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
28	    btn.addEventListener('click', () => sidebar?.classList.toggle('open'));
29	  });
30	}
31	
32	/* ---------------- Scroll reveal ---------------- */
33	function initFadeInOnScroll() {
34	  const items = document.querySelectorAll('.reveal');
35	  if (!items.length) return;
36	  const io = new IntersectionObserver((entries) => {
37	    entries.forEach(entry => {
38	      if (entry.isIntersecting) {
39	        entry.target.classList.add('is-visible');
40	        io.unobserve(entry.target);
41	      }
42	    });
43	  }, { threshold: 0.15 });
44	  items.forEach(el => io.observe(el));
45	}
46	
47	/* ---------------- AJAX helper ---------------- */
48	async function apiPost(url, data) {
49	  const res = await fetch(url, {
50	    method: 'POST',
51	    headers: { 'Content-Type': 'application/json' },
52	    body: JSON.stringify(data),
53	  });
54	  if (!res.ok) throw new Error('Request failed: ' + res.status);
55	  return res.json();
56	}
57	
58	/* ---------------- Live job search filter (AJAX demo) ---------------- */
59	const jobSearchInput = document.getElementById('jobSearchInput');
60	if (jobSearchInput) {
61	  let debounce;
62	  jobSearchInput.addEventListener('input', (e) => {
63	    clearTimeout(debounce);
64	    debounce = setTimeout(async () => {
65	      const q = e.target.value;
66	      const res = await fetch(`${window.BASE_URL}/student/ajax_job_search.php?q=${encodeURIComponent(q)}`);
67	      const html = await res.text();
68	      document.getElementById('jobResults').innerHTML = html;
69	    }, 300);
70	  });
71	}
