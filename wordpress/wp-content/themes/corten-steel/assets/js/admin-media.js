(function () {
	'use strict';

	function bindMedia(button) {
		var target = button.getAttribute('data-corten-media');
		var input = document.getElementById(target);
		var preview = document.getElementById(target + '-preview');
		if (!input || typeof wp === 'undefined' || !wp.media) {
			return;
		}

		var frame = wp.media({
			title: button.getAttribute('data-corten-title') || 'Select image',
			library: { type: 'image' },
			multiple: false,
		});

		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			var src = att.url;
			if (att.sizes && att.sizes.medium) {
				src = att.sizes.medium.url;
			}
			input.value = String(att.id);
			if (preview) {
				preview.innerHTML = '<img src="' + src + '" alt="" style="width:100%;height:8rem;object-fit:cover;display:block;">';
			}
		});

		frame.open();
	}

	function bindFile(button) {
		var target = button.getAttribute('data-corten-file');
		var input = document.getElementById(target);
		var preview = document.getElementById(target + '-preview');
		if (!input || typeof wp === 'undefined' || !wp.media) {
			return;
		}

		var frame = wp.media({
			title: button.getAttribute('data-corten-title') || 'Select PDF',
			library: { type: 'application/pdf' },
			multiple: false,
		});

		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			input.value = String(att.id);
			if (preview) {
				var name = att.filename || att.title || 'PDF';
				preview.innerHTML = '<a href="' + att.url + '" target="_blank" rel="noopener noreferrer">' + name + '</a>';
			}
		});

		frame.open();
	}

	document.addEventListener('click', function (event) {
		var selectBtn = event.target.closest('[data-corten-media]');
		if (selectBtn) {
			event.preventDefault();
			bindMedia(selectBtn);
			return;
		}

		var fileBtn = event.target.closest('[data-corten-file]');
		if (fileBtn) {
			event.preventDefault();
			bindFile(fileBtn);
			return;
		}

		var clearBtn = event.target.closest('[data-corten-media-clear]');
		if (clearBtn) {
			event.preventDefault();
			var key = clearBtn.getAttribute('data-corten-media-clear');
			var input = document.getElementById(key);
			var preview = document.getElementById(key + '-preview');
			if (input) {
				input.value = '';
			}
			if (preview) {
				preview.innerHTML = '';
			}
			return;
		}

		var clearFile = event.target.closest('[data-corten-file-clear]');
		if (clearFile) {
			event.preventDefault();
			var fileKey = clearFile.getAttribute('data-corten-file-clear');
			var fileInput = document.getElementById(fileKey);
			var filePreview = document.getElementById(fileKey + '-preview');
			if (fileInput) {
				fileInput.value = '';
			}
			if (filePreview) {
				filePreview.innerHTML = '<span style="color:#646970;">No PDF selected</span>';
			}
		}
	});
})();
