$(window).load(function () {
    $('#pre-loader').delay(250).fadeOut(function () {
        $('#pre-loader').remove();
    });
});

$(document).ready(function () {
    $.ajaxSetup({cache: false});
    //$("#sidebar").addClass('collapsed');

	
    //expand or collapse sidebar menu 
    $("#sidebar-toggle-md").click(function () {
		//$("#sidebar").toggleClass('no-hover');
        $("#sidebar").toggleClass('collapsed');
        if ($("#sidebar").hasClass("collapsed")) {
            $(this).find(".fa").removeClass("fa-dedent");
            $(this).find(".fa").addClass("fa-indent");
        } else {
            $(this).find(".fa").addClass("fa-dedent");
            $(this).find(".fa").removeClass("fa-indent");
        }
    });
	
    $("#sidebar-collapse").click(function () {
        $("#sidebar").addClass('collapsed');
    });
	/*
	$("#sidebar").hover(function(){
		if (!$("#sidebar").hasClass('no-hover')){
			$(this).toggleClass("collapsed");
		}
	});
	*/	
    //expand or collaps sidebar menu items
    $("#sidebar-menu > .expand > a").click(function () {
        var $target = $(this).parent();
        if ($target.hasClass('main')) {
            if ($target.hasClass('open')) {
                $target.removeClass('open');
            } else {
                $("#sidebar-menu >.expand").removeClass('open');
                $target.addClass('open');
            }
            if (!$(this).closest(".collapsed").length) {
                return false;
            }
        }
    });


    $("#sidebar-toggle").click(function () {
        $("body").toggleClass("off-screen");
        $("#sidebar").removeClass("collapsed");
    });
	
    //set custom scrollbar
    setPageScrollable();
    setMenuScrollable();
    $(window).resize(function () {
        //setPageScrollable(); // COMENTADO PARA QUE NO SUBA EL SCROLL AL ESTAR VIENDO REPORTES LARGOS Y CAMBIAR TAMAÑO DE LA VENTANA DE NAVEGADOR
        setMenuScrollable();
    });

    $('body').on('click', '.timeline-images a', function () {
        var $gallery = $(this).closest(".timeline-images");
        $gallery.magnificPopup({
            delegate: 'a',
            type: 'image',
            closeOnContentClick: false,
            closeBtnInside: false,
            mainClass: 'mfp-with-zoom mfp-img-mobile',
            gallery: {
                enabled: true
            },
            image: {
                titleSrc: 'data-title'
            },
            callbacks: {
                change: function (item) {

                    var itemData = $(item.el).data();
                    setTimeout(function () {
                        if (itemData && itemData.viewer === 'google') {
                            $(".mfp-content").addClass("full-width-mfp-content");
                        } else {
                            $(".mfp-content").removeClass("full-width-mfp-content");
                        }
                    });

                }
            }
        });
        $gallery.magnificPopup('open');
        return false;
    });


    //search datatable when clicks on the labels.

    $('body').on('click', '.label.clickable', function () {
        var value = $(this).text();

        $(this).closest(".dataTables_wrapper").find("input[type=search]").val(value).focus().select();
        $(this).closest(".dataTable").DataTable().search(value).draw();
        return false;
    });

    //add a hidden filed in form when clicking on delete file link
    $('body').on('click', '.delete-saved-file', function () {
        var fileName = $(this).attr("data-file_name");
        //add a hidden filed with the file name for delete
        $(this).closest(".saved-file-item-container").html("<input type='hidden' name=delete_file[] value='" + fileName + "' />");
        return false;
    });



});

//set scrollbar on page
setPageScrollable = function () {
    if ($.fn.mCustomScrollbar) {
        if ($(window).width() <= 640) {
            $('html').css({"overflow": "auto"});
            $('body').css({"overflow": "auto"});
        } else {
            initScrollbar('.scrollable-page', {
                setHeight: $(window).height() - 45
            });
        }
    }
};

//set scrollbar on left menu
setMenuScrollable = function () {
    initScrollbar('#sidebar-scroll', {
        setHeight: $(window).height() - 110
    });
};

initScrollbar = function (selector, options) {
    if (!options) {
        options = {};
    }

    var defaults = {
        theme: "minimal-dark",
        autoExpandScrollbar: true,
        keyboard: {
            enable: true,
            scrollType: "stepless",
            scrollAmount: 40
        },
        mouseWheelPixels: 300,
        scrollInertia: 60,
        mouseWheel: {scrollAmount: 188, normalizeDelta: true},
    },
    settings = $.extend({}, defaults, options);

    if (AppHelper.settings.scrollbar == "native") {
        $(selector).css({"height": settings.setHeight + "px", "overflow-y": "scroll"});
    } else {
        if ($.fn.mCustomScrollbar) {
			/*Se comenta la siguiente linea debido a que el scrollbar no estaba funcionando al 
			cargarse la página, sólo al momento del resize de la ventana */
            $(selector).mCustomScrollbar("destroy");

            $(selector).mCustomScrollbar(settings);
        }
    }

};

// generate reandom string 
getRndomString = function (length) {
    var result = '',
            //chars = '!-().0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for (var i = length; i > 0; --i)
        result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
};


// getnerat random small alphabet 
getRandomAlphabet = function (length) {
    var result = '',
            chars = 'abcdefghijklmnopqrstuvwxyz';
    for (var i = length; i > 0; --i)
        result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
};


attachDropzoneWithForm = function (dropzoneTarget, uploadUrl, validationUrl, options) {
    var $dropzonePreviewArea = $(dropzoneTarget),
            $dropzonePreviewScrollbar = $dropzonePreviewArea.find(".post-file-dropzone-scrollbar"),
            $previews = $dropzonePreviewArea.find(".post-file-previews"),
            $postFileUploadRow = $dropzonePreviewArea.find(".post-file-upload-row"),
            $uploadFileButton = $dropzonePreviewArea.find(".upload-file-button"),
            $submitButton = $dropzonePreviewArea.find("button[type=submit]"),
            previewsContainer = getRandomAlphabet(15),
            postFileUploadRowId = getRandomAlphabet(15),
            uploadFileButtonId = getRandomAlphabet(15);
	//console.log($(dropzoneTarget));
	//console.log(document.getElementById(dropzoneTarget));

    //set random id with the previws 
	//console.log($dropzonePreviewScrollbar);
    $previews.attr("id", previewsContainer);
    $postFileUploadRow.attr("id", postFileUploadRowId);
    $uploadFileButton.attr("id", uploadFileButtonId);
	//console.log($(dropzoneTarget));

    //get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#" + postFileUploadRowId);
	//console.log(previewNode);
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    if (!options)
        options = {};

    var postFilesDropzone = new Dropzone(dropzoneTarget, {
        url: uploadUrl,
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        maxFilesize: 3000,
        previewTemplate: previewTemplate,
        dictDefaultMessage: AppLanugage.fileUploadInstruction,
        autoQueue: true,
        previewsContainer: "#" + previewsContainer,
        //clickable: "#" + uploadFileButtonId,
        maxFiles: options.maxFiles ? options.maxFiles : 1000,
        init: function () {
            this.on("maxfilesexceeded", function (file) {
                this.removeAllFiles();
                this.addFile(file);
            });
        },
        accept: function (file, done) {
            if (file.name.length > 200) {
                done(AppLanugage.fileNameTooLong);
            }

            $dropzonePreviewScrollbar.removeClass("hide");
            initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

            $dropzonePreviewScrollbar.parent().removeClass("hide");
            $dropzonePreviewArea.find("textarea").focus();
            //validate the file
            $.ajax({
                url: validationUrl,
                data: {file_name: file.name, file_size: file.size},
                cache: false,
                type: 'POST',
                dataType: "json",
                success: function (response) {
                    if (response.success) {

                        $(file.previewTemplate).append("<input type='hidden' name='file_names[]' value='" + file.name + "' />\n\
                                 <input type='hidden' name='file_sizes[]' value='" + file.size + "' />");
                        done();
                    } else {
                        appAlert.error(response.message);
                        $(file.previewTemplate).find("input").remove();
                        done(response.message);

                    }
                }
            });
        },
        processing: function () {
            $submitButton.prop("disabled", true);
        },
        queuecomplete: function () {
            $submitButton.prop("disabled", false);
        },
        reset: function (file) {
            $dropzonePreviewScrollbar.addClass("hide");
        },
        fallback: function () {
            //add custom fallback;
            $("body").addClass("dropzone-disabled");

            $uploadFileButton.click(function () {
                //fallback for old browser
                $(this).html("<i class='fa fa-camera'></i> Add more");

                $dropzonePreviewScrollbar.removeClass("hide");
                initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

                $dropzonePreviewScrollbar.parent().removeClass("hide");
                $previews.prepend("<div class='clearfix p5 file-row'><button type='button' class='btn btn-xs btn-danger pull-left mr10 remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>");

            });
            $previews.on("click", ".remove-file", function () {
                $(this).parent().remove();
            });
        },
        success: function (file) {
            setTimeout(function () {
                $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
            }, 1000);
        }
    });

    return postFilesDropzone;
};

teamAndMemberSelect2Format = function (option) {
    if (option.type === "team") {
        return "<i class='fa fa-users info'></i> " + option.text;
    } else {
        return "<i class='fa fa-user'></i> " + option.text;
    }
};

setDatePicker = function (element, options) {
    if (!options) {
        options = {};
    }
    var settings = $.extend({}, {
        autoclose: true,
        language: "custom",
        todayHighlight: true,
        weekStart: AppHelper.settings.firstDayOfWeek,
        format: "yyyy-mm-dd"
    }, options);

    $(element).datepicker(settings);
};

setTimePicker = function (element, options) {
    if (!options) {
        options = {};
    }

    var showMeridian = AppHelper.settings.timeFormat == "24_hours" ? false : true;

    var settings = $.extend({}, {
        minuteStep: 5,
        defaultTime: "",
        appendWidgetTo: "#ajaxModal",
        showMeridian: showMeridian
    }, options);

    $(element).timepicker(settings);
};


initWYSIWYGEditor = function (element, options) {
    if (!options) {
        options = {};
    }

    var settings = $.extend({}, {
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['hr']],
            ['view', ['fullscreen', 'codeview']]
        ],
        disableDragAndDrop: true
    }, options);

    $(element).summernote(settings);
};

getWYSIWYGEditorHTML = function (element) {
    return $(element).summernote('code');
};

combineCustomFieldsColumns = function (defaultFields, customFieldString) {
    if (defaultFields && customFieldString) {

        var startAfter = defaultFields.slice(-1)[0];
        //count no of custom fields
        var noOfCustomFields = customFieldString.split(',').length;
        if (noOfCustomFields) {
            for (var i = 1; i <= noOfCustomFields; i++) {
                defaultFields.push(i + startAfter);
                startAfter++;
            }
        }
    }
    return defaultFields;
};

numberFormat = function(number, decimals, dec_point, thousands_sep){
	
	// Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
	
};

$.validator.addMethod(
	"regex",
	function(value, element, regexp) {
		var re = new RegExp(regexp);
		return this.optional(element) || re.test(value);
	},
	"Please check your input."
);

/*
$.validator.addMethod("slider", function(value, element){
	return (this.optional(element) || $(element).slider("value") == 100);
}, "Slider not equal to 100");
*/

$.validator.addMethod("equals", function(value, element, param) { 
  return this.optional(element) || value == param; 
}, $.validator.format("You must enter {0}"));

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
	/*"extract-date-pre": function(value) {
		//var date = value.split('/');
		console.log(value);
		return value;
	},*/
	
	"extract-date-asc": function(a, b) {
		//console.log(a);
		//var fecha_a = moment(a, 'DD/MM/YYYY');
		//var fecha_b = moment(b, 'DD/MM/YYYY');
        if(a == '-' && b != '-'){
            return 1;
        }
        if(a != '-' && b == '-'){
            return -1;
        }
		var fecha_a = moment(a, date_format_to_moment(AppHelper.settings.dateFormat));
		var fecha_b = moment(b, date_format_to_moment(AppHelper.settings.dateFormat));
		
		if(fecha_a.isBefore(fecha_b)){
			return -1;
		}else{
			if(fecha_a.isAfter(fecha_b)){
				return 1;
			}else{
				return 0;
			}
		}
		
		//return ((a < b) ? -1 : ((a > b) ? 1 : 0));
	},
	"extract-date-desc": function(a, b) {

		//var fecha_a = moment(a, 'DD/MM/YYYY');
        //var fecha_b = moment(b, 'DD/MM/YYYY');
        if(a == '-' && b != '-'){
            return -1;
        }
        if(a != '-' && b == '-'){
            return 1;
        }

		var fecha_a = moment(a, date_format_to_moment(AppHelper.settings.dateFormat));
		var fecha_b = moment(b, date_format_to_moment(AppHelper.settings.dateFormat));
		
		if(fecha_a.isBefore(fecha_b)){
			return 1;
		}else{
			if(fecha_a.isAfter(fecha_b)){
				return -1;
			}else{
				return 0;
			}
		}
		//return ((a < b) ? 1 : ((a > b) ? -1 : 0));
	}
});

function date_format_to_moment(date){
	var fecha = '';
	if(date == 'd-m-Y'){
		fecha = 'DD-MM-YYYY';
	}
	if(date == 'm-d-Y'){
		fecha = 'MM-DD-YYYY';
	}
	if(date == 'Y-m-d'){
		fecha = 'YYYY-MM-DD';
	}
	if(date == 'd/m/Y'){
		fecha = 'DD/MM/YYYY';
	}
	if(date == 'm/d/Y'){
		fecha = 'MM/DD/YYYY';
	}
	if(date == 'Y/m/d'){
		fecha = 'YYYY/MM/DD';
	}
	if(date == 'd.m.Y'){
		fecha = 'DD.MM.YYYY';
	}
	if(date == 'm.d.Y'){
		fecha = 'MM.DD.YYYY';
	}
	if(date == 'Y.m.d'){
		fecha = 'YYYY.MM.DD';
	}
	
	return fecha;
}

function select2LoadingStatusOn(element){
    $(element).select2("enable", false);
    $(element).select2("data", {id: "", text: AppLanugage.loading});
}

function select2LoadingStatusOff(element){
	$(element).select2("enable", true);
    $(element).select2("val", "");
}

Highcharts.setOptions({
   lang:{
      resetZoom: AppLanugage.resetZoom,
      resetZoomTitle: AppLanugage.resetZoomTitle,
	  //thousandsSep: '.'
	  numericSymbols: null //otherwise by default ['k', 'M', 'G', 'T', 'P', 'E']
   }
});

$.fn.select2.defaults.formatNoMatches = AppLanugage.formatNoMatches;