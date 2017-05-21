String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
String.prototype.ltrim=function(){return this.replace(/^\s+/,'');};
String.prototype.rtrim=function(){return this.replace(/\s+$/,'');};
String.prototype.fulltrim=function(){return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');};

var updatePlace = function(input) {
	var $input = $(input);
	console.log($input.val());
	var $form = $input.closest('form');
	$form.attr('action', $form.attr('action').replace(/(placeId=)\d*/mg, '$1' + $input.val()));
};

$(document).ready(function() {

	function colorTable() {

		$('.container table > tbody > tr').removeClass('odd');
		$('.container table > tbody > tr:visible:odd').addClass('odd');
	}

	colorTable();


	$('.table-actions').each(function() {
		var $actionRow = $(this);
		$actionRow.prev('tr').on('click', function(e) {
			var $target = $(e.target);
			if ($(window).width() > 768) return;
			if (!$target.hasClass('navigationLink')) {
				e.preventDefault();
				e.stopPropagation();
			}
			
			// hide other
			// $('tr.active').find('.glyphicon-collapse-down').toggleClass('.glyphicon-collapse-down');
			// $('tr.active').next('.table-actions').hide();
			// $('tr.active').toggleClass('.active');

			var $row = $(this);
			$row.find('.glyphicon-expand').toggleClass('glyphicon-collapse-down');
			$row.toggleClass('active');
			$actionRow.toggle();
			if (!$target.hasClass('navigationLink')) {
				return false;
			}
		});
	});

	$('.decreaseCount').on('click', function(e) {
		var input = $(this).closest('.input-group').find('#count');
		var value = parseInt(input.val(), 10);
		if (value <= 0) return;
		input.val(value - 1);
	});

	$('.increaseCount').on('click', function(e) {
		var input = $(this).closest('.input-group').find('#count');
		input.val(parseInt(input.val(), 10) + 1);
	});


	if (window.chrome || navigator.appVersion.indexOf("Linux") != -1 || navigator.appVersion.indexOf("X11") != -1) {
		$('head').append('<link rel="stylesheet" href="//cdn.jsdelivr.net/emojione/1.3.0/assets/css/emojione.min.css" />')
		$.getScript('//cdn.jsdelivr.net/emojione/1.3.0/lib/js/emojione.min.js', function() {
			var emojiBlock = $('.emojisupport').children();
			if (!emojiBlock.length) emojiBlock = $('.emojisupport');
			emojiBlock.each(function() {
				$(this).html(emojione.unicodeToImage($(this).text()));
			});
		});
	}

	$('form').validator().on('submit', function (e) {
		if (e.isDefaultPrevented()) {
			return false;
	 	} else {
			return true;
		}
	});

	$('.btn-category').on('click', function(e) {
		var $button = $(this);
		var $categoriesBtn = $('input[name=categories]');
		var categories = $categoriesBtn.val();
		$button.find('.glyphicon').toggleClass('glyphicon-ok-sign');
		if (!$button.hasClass('active')) {
			categories  += $button.data('categoryId') + ' ';
		} else {
			categories = categories.replace($button.data('categoryId'), '');
		}
		categories = categories.ltrim();
		$categoriesBtn.val(categories);
	});

	var $categoriesInputs = $('input[name=categories]');
	$categoriesInputs.each(function() {
		var $categoriesInput = $(this);
		var value = $.trim($categoriesInput.val());
		if (value.length) {
			var categoriesArray = value.split(' ');
			for (var i in categoriesArray) {
				var $button = $categoriesInput.closest('div').find('[data-category-id=' + categoriesArray[i] + ']');
				if (!$button.hasClass('active')) {
					$button.addClass('active');
					$button.find('.glyphicon').toggleClass('glyphicon-ok-sign');
				}
			}
		}
		
	});

	$('.remoteAction').on('click', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $link = $(this);
		var target = $link.attr('href');
		var successAction = $link.data('success-action');
		var isIconLink = $link.closest('.iconcell').length;
		var $remoteContainer = $link.closest('.remoteContainer');
		if (!$remoteContainer.hasClass('forceDelete')) {
			if (successAction === 'remove' && !confirm("Wirklich löschen?")) {
				return false;
			}
		}
		$.post(target, {
			beforeSend: function() {
				if (isIconLink) {
					$link.find('.glyphicon').css('visibility', 'hidden');
					$link.addClass('loading-icon-link');
				}
			}, 
			complete: function() {
				if (isIconLink) {
					$link.find('.glyphicon').css('visibility', '');
					$link.removeClass('loading-icon-link');
				}
			}
		}, function() {
			var $counter;
			if ($remoteContainer.hasClass('table-actions')) {
				$counter = $remoteContainer.prev('tr').find('.remoteCounter');
			} else {
				$counter = $remoteContainer.find('.remoteCounter');
			}
			var counterValue = parseInt($counter.text(), 10);
			if (successAction == 'increase') {
				$counter.text(counterValue + 1);
			} else if (successAction == 'decrease') {
				$counter.text(parseInt($counter.text(), 10) - 1);
				if (counterValue <= 1) {
					if (confirm('Eintrag löschen?')) {
						$remoteContainer.addClass('forceDelete');
						$remoteContainer.find('[data-success-action=remove]').trigger('click');
					}
				}
			} else if (successAction == 'remove') {
				if ($remoteContainer.hasClass('table-actions')) {
					$remoteContainer = $remoteContainer.add($remoteContainer.prev('tr'));
				} else {
					$remoteContainer = $remoteContainer.add($remoteContainer.next('tr'));	
				}
				$remoteContainer.hide().remove();
				colorTable();
			}
		});
		return false;
	});
});
