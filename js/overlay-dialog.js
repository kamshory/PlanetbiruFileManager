function overlayDialog(content, width, height) {
	width += 20;
	height += 20;

	$('#overlay-container').remove();

	let html = `
    <div id="overlay-container">
        <div class="ui-widget-overlay"></div>

        <div id="overlay-inner"
             class="ui-widget ui-widget-content ui-corner-all"
             style="width:${width}px;height:${height}px">

            <div class="ui-dialog-content">
                ${content}
            </div>

        </div>
    </div>
    `;

	$('body').append(html);

	// Click outside dialog → close
	$('#overlay-container').on('click', function () {
		closeOverlayDialog();
	});

	// Prevent close when clicking inside dialog
	$('#overlay-inner').on('click', function (e) {
		e.stopPropagation();
	});

	// ESC or ENTER closes
	$(document).on('keydown.overlay', function (e) {
		if (e.key === "Escape" || e.key === "Enter") {
			closeOverlayDialog();
		}
	});
}

function closeOverlayDialog() {
    $('#overlay-container').fadeOut(200, function () {
        $(this).remove();
    });

    // Remove event namespace
    $(document).off('keydown.overlay');
}
