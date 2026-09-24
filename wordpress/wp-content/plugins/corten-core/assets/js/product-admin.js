(function () {
	'use strict';

	function galleryInput() {
		return document.getElementById('corten_gallery');
	}

	function galleryPreview() {
		return document.getElementById('corten_gallery-preview');
	}

	function currentIds() {
		var input = galleryInput();
		if (!input || !input.value) {
			return [];
		}
		return input.value.split(',').map(function (id) {
			return parseInt(id, 10);
		}).filter(function (id) {
			return id > 0;
		});
	}

	function setIds(ids) {
		var input = galleryInput();
		if (input) {
			input.value = ids.join(',');
		}
	}

	function appendPreview(id, src) {
		var preview = galleryPreview();
		if (!preview) {
			return;
		}
		var wrap = document.createElement('span');
		wrap.className = 'corten-gallery-preview__item';
		wrap.setAttribute('data-id', String(id));
		wrap.style.cssText = 'position:relative;width:7.5rem;';
		wrap.innerHTML = '<img src="' + src + '" alt="" style="width:7.5rem;height:7.5rem;object-fit:cover;display:block;background:#1a1a1a;">' +
			'<button type="button" class="button-link" data-corten-gallery-remove="' + id + '" style="position:absolute;top:0.2rem;right:0.35rem;color:#fff;background:#111;padding:0 0.35rem;">×</button>';
		preview.appendChild(wrap);
	}

	document.addEventListener('click', function (event) {
		var addBtn = event.target.closest('[data-corten-gallery-add]');
		if (addBtn) {
			event.preventDefault();
			if (typeof wp === 'undefined' || !wp.media) {
				return;
			}
			var frame = wp.media({
				title: 'Select gallery images',
				library: { type: 'image' },
				multiple: true,
			});
			frame.on('select', function () {
				var ids = currentIds();
				frame.state().get('selection').each(function (model) {
					var att = model.toJSON();
					if (ids.indexOf(att.id) !== -1) {
						return;
					}
					ids.push(att.id);
					var src = att.url;
					if (att.sizes && att.sizes.medium) {
						src = att.sizes.medium.url;
					}
					appendPreview(att.id, src);
				});
				setIds(ids);
			});
			frame.open();
			return;
		}

		var removeBtn = event.target.closest('[data-corten-gallery-remove]');
		if (removeBtn) {
			event.preventDefault();
			var removeId = parseInt(removeBtn.getAttribute('data-corten-gallery-remove'), 10);
			setIds(currentIds().filter(function (id) {
				return id !== removeId;
			}));
			var item = removeBtn.closest('[data-id]');
			if (item) {
				item.remove();
			}
			return;
		}

		var clearBtn = event.target.closest('[data-corten-gallery-clear]');
		if (clearBtn) {
			event.preventDefault();
			setIds([]);
			var preview = galleryPreview();
			if (preview) {
				preview.innerHTML = '';
			}
			return;
		}

		var addRow = event.target.closest('[data-corten-dim-add]');
		if (addRow) {
			event.preventDefault();
			var template = document.getElementById('corten-dim-template');
			var tbody = document.getElementById('corten-dim-rows');
			if (!template || !tbody) {
				return;
			}
			var html = template.innerHTML.replace(/__i__/g, String(Date.now()));
			tbody.insertAdjacentHTML('beforeend', html);
			return;
		}

		var removeRow = event.target.closest('[data-corten-dim-remove]');
		if (removeRow) {
			event.preventDefault();
			var row = removeRow.closest('tr');
			if (row) {
				row.remove();
			}
		}
	});
})();
