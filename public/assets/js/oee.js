var csrf_token = $('meta[name=csrf-token]').attr('content');

$.ajaxSetup({ headers: {'X-CSRF-TOKEN': csrf_token}});

var Oee = function() {
    return {

        blockUI: function(options) {
            options = $.extend(true, {}, options);
            var html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';

            if (options.custom){
                html = '<div class="h-100 loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><div class="h-100">' + (options.message ? options.message : 'LOADING...') + '</div></div>';
                $(options.target).block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: false,
                    centerX: false,
                    css: {
                        top:0,
                        left:0,
                        border:0,
                        padding:0,
                        margin:0,
                        width:'100%',
                        height:'100%',
                        textAlign:'center',
                        color:'#000',
                        backgroundColor:'#F2F2F2',
                        cursor:'wait',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                    },
                    overlayCSS: {
                        top:'30%',
                        backgroundColor: '#F2F2F2',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait',
                        borderRadius: '1rem'
                    }
                });
            } else if (options.target) { // element blocking
                var el = $(options.target);
                if (el.height() <= ($(window).height())) {
                    options.cenrerY = true;
                }
                el.block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                    css: {
                        top: '10%',
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait',
                        borderRadius: '1rem'
                    }
                });
            }else {
                $.blockUI({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    css: {
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            }
        },

        unblockUI: function(target) {
            if (target) {
                $(target).unblock({
                    onUnblock: function() {
                        $(target).css('position', '');
                        $(target).css('zoom', '');
                    }
                });
            } else {
                $.unblockUI();
            }
        },
    };
}();
