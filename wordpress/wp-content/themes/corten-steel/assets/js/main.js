/**
 * Oxiron front-end interactions. Vanilla JS only.
 */
(() => {
	const root = document.documentElement;
	const header = document.querySelector("[data-site-header]");
	const navToggle = document.querySelector("[data-nav-toggle]");
	const themeToggle = document.querySelector("[data-theme-toggle]");
	const themeLabel = document.querySelector("[data-theme-label]");
	const cursor = document.querySelector("[data-cursor]");
	const progressBar = document.querySelector("[data-scroll-progress] span");
	const parallaxMedia = document.querySelector("[data-parallax]");
	const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	const finePointer = window.matchMedia("(pointer: fine)").matches;

	const storedTheme = window.localStorage.getItem("corten-theme");
	const theme = storedTheme || "dark";
	root.setAttribute("data-theme", theme);
	syncThemeToggle(theme);

	function syncThemeToggle(next) {
		if (!themeToggle || !themeLabel) {
			return;
		}
		themeToggle.setAttribute("aria-pressed", next === "dark" ? "true" : "false");
		themeLabel.textContent = next === "dark" ? "Dark" : "Light";
	}

	themeToggle?.addEventListener("click", () => {
		const next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
		root.setAttribute("data-theme", next);
		window.localStorage.setItem("corten-theme", next);
		syncThemeToggle(next);
	});

	/* ---- Scroll: solid header, progress bar, hero parallax ---- */
	let ticking = false;
	const onScroll = () => {
		if (ticking) {
			return;
		}
		ticking = true;
		window.requestAnimationFrame(() => {
			const y = window.scrollY;
			if (header) {
				header.classList.toggle("is-solid", y > 100);
			}
			if (progressBar) {
				const doc = document.documentElement;
				const max = doc.scrollHeight - doc.clientHeight;
				const pct = max > 0 ? Math.min((y / max) * 100, 100) : 0;
				progressBar.style.transform = `scaleX(${pct / 100})`;
			}
			if (parallaxMedia && !reduceMotion) {
				// Only parallax while the hero is near the viewport.
				const hero = parallaxMedia.closest(".hero");
				if (hero) {
					const rect = hero.getBoundingClientRect();
					if (rect.bottom > 0 && rect.top < window.innerHeight) {
						parallaxMedia.style.transform = `translate3d(0, ${y * 0.22}px, 0)`;
					}
				}
			}
			ticking = false;
		});
	};
	onScroll();
	window.addEventListener("scroll", onScroll, { passive: true });

	navToggle?.addEventListener("click", () => {
		const open = header.classList.toggle("is-open");
		navToggle.setAttribute("aria-expanded", open ? "true" : "false");
	});

	/* ---- Reveal on scroll ---- */
	if (!reduceMotion) {
		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add("is-visible");
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.16, rootMargin: "0px 0px -8% 0px" }
		);
		document.querySelectorAll(".reveal").forEach((node) => observer.observe(node));
	} else {
		document.querySelectorAll(".reveal").forEach((node) => node.classList.add("is-visible"));
	}

	/* ---- Animated counters ---- */
	document.querySelectorAll("[data-count]").forEach((node) => {
		const target = Number(node.getAttribute("data-count") || 0);
		const suffix = node.getAttribute("data-suffix") || "";
		const run = () => {
			if (reduceMotion) {
				node.textContent = `${target.toLocaleString()}${suffix}`;
				return;
			}
			const start = performance.now();
			const duration = 1100;
			const tick = (now) => {
				const progress = Math.min((now - start) / duration, 1);
				const eased = 1 - Math.pow(1 - progress, 3);
				node.textContent = `${Math.round(target * eased).toLocaleString()}${suffix}`;
				if (progress < 1) {
					window.requestAnimationFrame(tick);
				}
			};
			window.requestAnimationFrame(tick);
		};

		if (reduceMotion) {
			run();
			return;
		}

		const statsObserver = new IntersectionObserver((entries, obs) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					run();
					obs.unobserve(entry.target);
				}
			});
		});
		statsObserver.observe(node);
	});

	/* ---- Case carousel ---- */
	const track = document.querySelector("[data-carousel-track]");
	document.querySelector("[data-carousel-prev]")?.addEventListener("click", () => {
		track?.scrollBy({ left: -track.clientWidth * 0.8, behavior: reduceMotion ? "auto" : "smooth" });
	});
	document.querySelector("[data-carousel-next]")?.addEventListener("click", () => {
		track?.scrollBy({ left: track.clientWidth * 0.8, behavior: reduceMotion ? "auto" : "smooth" });
	});

	/* ---- Catalog AJAX filters ---- */
	const filterForm = document.querySelector("[data-product-filters]");
	const grid = document.querySelector("[data-product-grid]");
	const count = document.querySelector("[data-filter-count]");

	const bindReveals = (scope) => {
		if (reduceMotion) {
			scope.querySelectorAll(".reveal").forEach((node) => node.classList.add("is-visible"));
			return;
		}
		const observer = new IntersectionObserver((entries, obs) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add("is-visible");
					obs.unobserve(entry.target);
				}
			});
		});
		scope.querySelectorAll(".reveal").forEach((node) => observer.observe(node));
	};

	const applyFilters = () => {
		if (!filterForm || !grid || typeof window.cortenTheme === "undefined") {
			return;
		}
		const data = new FormData(filterForm);
		data.append("action", "corten_filter_products");
		data.append("nonce", window.cortenTheme.nonce);
		grid.setAttribute("aria-busy", "true");
		fetch(window.cortenTheme.ajaxUrl, {
			method: "POST",
			credentials: "same-origin",
			body: data,
		})
			.then((response) => response.json())
			.then((payload) => {
				if (!payload?.success) {
					return;
				}
				grid.innerHTML = payload.data.html || `<p class="empty-state">${window.cortenTheme.i18n.empty}</p>`;
				if (count) {
					count.textContent = `${payload.data.count} product${payload.data.count === 1 ? "" : "s"}`;
				}
				bindReveals(grid);
			})
			.finally(() => grid.removeAttribute("aria-busy"));
	};

	filterForm?.addEventListener("change", applyFilters);

	/* ---- Product gallery ---- */
	const galleryRoot = document.querySelector("[data-product-gallery]");
	const mainImage = document.querySelector("[data-gallery-main]");
	const thumbs = Array.from(document.querySelectorAll("[data-gallery-thumb]"));

	const showGalleryIndex = (index) => {
		if (!mainImage || !thumbs.length) {
			return;
		}
		const next = ((index % thumbs.length) + thumbs.length) % thumbs.length;
		const thumb = thumbs[next];
		const src = thumb.getAttribute("data-gallery-thumb");
		if (src) {
			mainImage.src = src;
		}
		thumbs.forEach((node) => node.removeAttribute("aria-current"));
		thumb.setAttribute("aria-current", "true");
	};

	const currentGalleryIndex = () => {
		const active = thumbs.findIndex((node) => node.getAttribute("aria-current") === "true");
		return active >= 0 ? active : 0;
	};

	thumbs.forEach((thumb, index) => {
		thumb.addEventListener("click", () => showGalleryIndex(index));
	});

	document.querySelector("[data-gallery-prev]")?.addEventListener("click", (event) => {
		event.preventDefault();
		event.stopPropagation();
		showGalleryIndex(currentGalleryIndex() - 1);
	});

	document.querySelector("[data-gallery-next]")?.addEventListener("click", (event) => {
		event.preventDefault();
		event.stopPropagation();
		showGalleryIndex(currentGalleryIndex() + 1);
	});

	galleryRoot?.addEventListener("keydown", (event) => {
		if (!thumbs.length) {
			return;
		}
		if (event.key === "ArrowLeft") {
			event.preventDefault();
			showGalleryIndex(currentGalleryIndex() - 1);
		}
		if (event.key === "ArrowRight") {
			event.preventDefault();
			showGalleryIndex(currentGalleryIndex() + 1);
		}
	});

	const dialog = document.querySelector("[data-gallery-dialog]");
	const zoom = document.querySelector("[data-gallery-zoom]");
	document.querySelector("[data-gallery-open]")?.addEventListener("click", () => {
		if (!dialog || !zoom || !mainImage) {
			return;
		}
		zoom.src = mainImage.src;
		if (typeof dialog.showModal === "function") {
			dialog.showModal();
		}
	});
	document.querySelector("[data-gallery-close]")?.addEventListener("click", () => dialog?.close());
	dialog?.addEventListener("click", (event) => {
		if (event.target === dialog) {
			dialog.close();
		}
	});

	/* ---- Magnetic buttons ---- */
	if (finePointer && !reduceMotion) {
		document.querySelectorAll("[data-magnetic]").forEach((el) => {
			el.addEventListener("pointermove", (e) => {
				const rect = el.getBoundingClientRect();
				const x = e.clientX - rect.left - rect.width / 2;
				const y = e.clientY - rect.top - rect.height / 2;
				el.style.transform = `translate(${x * 0.18}px, ${y * 0.25}px)`;
				const label = el.querySelector(".btn__label");
				if (label) {
					label.style.transform = `translate(${x * 0.12}px, ${y * 0.18}px)`;
				}
			});
			el.addEventListener("pointerleave", () => {
				el.style.transform = "";
				const label = el.querySelector(".btn__label");
				if (label) {
					label.style.transform = "";
				}
			});
		});
	}

	/* ---- Floating ember particles ---- */
	const emberCanvas = document.querySelector("[data-ember-canvas]");
	const particlesEnabled =
		root.style.getPropertyValue("--corten-particles").trim() !== "0" &&
		getComputedStyle(root).getPropertyValue("--corten-particles").trim() !== "0";

	if (emberCanvas && finePointer && !reduceMotion && particlesEnabled) {
		const ctx = emberCanvas.getContext("2d");
		let particles = [];
		let width = 0;
		let height = 0;
		let raf = null;

		const resize = () => {
			const dpr = Math.min(window.devicePixelRatio || 1, 2);
			width = window.innerWidth;
			height = window.innerHeight;
			emberCanvas.width = width * dpr;
			emberCanvas.height = height * dpr;
			emberCanvas.style.width = `${width}px`;
			emberCanvas.style.height = `${height}px`;
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
		};
		resize();
		window.addEventListener("resize", resize);

		const EMBER_COUNT = Math.min(28, Math.floor((width * height) / 55000));
		const colors = ["#d97706", "#b85c38", "#f59e0b", "#8b6f47"];

		const spawn = () => ({
			x: Math.random() * width,
			y: height + Math.random() * 60,
			r: Math.random() * 1.6 + 0.4,
			vx: (Math.random() - 0.5) * 0.3,
			vy: -(Math.random() * 0.5 + 0.18),
			alpha: Math.random() * 0.5 + 0.2,
			color: colors[Math.floor(Math.random() * colors.length)],
			life: 0,
			ttl: Math.random() * 400 + 300,
		});

		for (let i = 0; i < EMBER_COUNT; i++) {
			particles.push({ ...spawn(), y: Math.random() * height });
		}

		const draw = () => {
			ctx.clearRect(0, 0, width, height);
			for (const p of particles) {
				p.x += p.vx + Math.sin((p.life + p.ttl) * 0.01) * 0.15;
				p.y += p.vy;
				p.life++;
				p.alpha = Math.max(0, p.alpha - 0.0006);
				if (p.y < -10 || p.life > p.ttl || p.alpha <= 0) {
					Object.assign(p, spawn());
				}
				ctx.beginPath();
				ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
				ctx.fillStyle = p.color;
				ctx.globalAlpha = p.alpha;
				ctx.shadowColor = p.color;
				ctx.shadowBlur = 6;
				ctx.fill();
			}
			ctx.globalAlpha = 1;
			ctx.shadowBlur = 0;
			raf = window.requestAnimationFrame(draw);
		};
		draw();

		// Pause when tab hidden to save battery.
		document.addEventListener("visibilitychange", () => {
			if (document.hidden) {
				window.cancelAnimationFrame(raf);
			} else {
				draw();
			}
		});
	}

	/* ---- Cursor follower (enhanced with hover ring) ---- */
	if (cursor && finePointer && !reduceMotion) {
		cursor.hidden = false;
		let x = 0;
		let y = 0;
		let currentX = 0;
		let currentY = 0;
		window.addEventListener(
			"pointermove",
			(event) => {
				x = event.clientX;
				y = event.clientY;
			},
			{ passive: true }
		);
		const follow = () => {
			currentX += (x - currentX) * 0.18;
			currentY += (y - currentY) * 0.18;
			cursor.style.transform = `translate3d(${currentX}px, ${currentY}px, 0) translate(-50%, -50%)`;
			window.requestAnimationFrame(follow);
		};
		window.requestAnimationFrame(follow);

		const interactive = "a, button, [data-magnetic], input, select, textarea, [role='button']";
		document.querySelectorAll(interactive).forEach((el) => {
			el.addEventListener("pointerenter", () => cursor.classList.add("is-hover"));
			el.addEventListener("pointerleave", () => cursor.classList.remove("is-hover"));
		});
		// Re-bind for dynamically injected filter results.
		const body = document.body;
		body.addEventListener("pointerover", (e) => {
			if (e.target.closest(interactive)) {
				cursor.classList.add("is-hover");
			}
		});
		body.addEventListener("pointerout", (e) => {
			if (e.target.closest(interactive)) {
				cursor.classList.remove("is-hover");
			}
		});
	}

	document.querySelectorAll("[data-file-field]").forEach((field) => {
		const input = field.querySelector('input[type="file"]');
		const nameEl = field.querySelector("[data-file-name]");
		if (!input || !nameEl) {
			return;
		}
		const placeholder = nameEl.getAttribute("data-file-placeholder") || nameEl.textContent.trim();
		input.addEventListener("change", () => {
			const file = input.files?.[0];
			nameEl.textContent = file ? file.name : placeholder;
		});
	});
})();
