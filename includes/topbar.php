1	<?php $user = currentUser(); ?>
2	<header class="topbar">
3	  <div style="display:flex; align-items:center; gap:1rem;">
4	    <button class="btn btn-ghost d-md-none" data-sidebar-toggle><i class="bi bi-list" style="font-size:1.3rem;"></i></button>
5	    <div>
6	      <div style="font-weight:700; font-size:1rem;"><?= e($pageTitle ?? 'Dashboard') ?></div>
7	      <div class="text-muted-2" style="font-size:.78rem;"><?= e(roleName((int) $_SESSION['role_id'])) ?></div>
8	    </div>
9	  </div>
10	
11	  <div style="display:flex; align-items:center; gap:1rem;">
12	    <button class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode"></button>
13	    <a href="#" class="btn btn-ghost" style="position:relative; padding:.5rem;">
14	      <i class="bi bi-bell" style="font-size:1.15rem;"></i>
15	    </a>
16	    <div style="display:flex; align-items:center; gap:.6rem;">
17	      <div style="width:36px; height:36px; border-radius:50%; background:var(--indigo-500); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.85rem;">
18	        <?= e(strtoupper(substr($user['full_name'] ?? 'U', 0, 1))) ?>
19	      </div>
20	      <div class="d-none d-md-block" style="font-size:.85rem; font-weight:600;"><?= e($user['full_name'] ?? '') ?></div>
21	    </div>
22	  </div>
23	</header>
