(function () {
    tinymce.PluginManager.add('ecwd', function (editor_calendar, url) {
        var sh_tag = 'ecwd';

        //helper functions
        function getAttr(s, n) {
            n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
            return n ? window.decodeURIComponent(n[1]) : '';
        };

        function html(cls, data) {
            var placeholder = ecwd_plugin.url + 'assets/calendar_icon_90x90.png';
            data = window.encodeURIComponent(data);
            return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
        }

        function replaceShortcodes(content) {
            //match [ecwd_calendar(attr)]
            return content.replace(/\[ecwd( [^\]]*)\]/g, function (all, attr) {
                return html('wp-ecwd-calendar', attr);
            });
        }

        function restoreShortcodes(content) {
            //match any image tag with our class and replace it with the shortcode's content and attributes
            return content.replace(/(?:<p(?: [^>]+)?>)*(<img class="mceItem wp-ecwd-calendar" [^>]+>)(?:<\/p>)*/g, function (match, image) {
                var data = getAttr(image, 'data-sh-attr');
                var con = getAttr(image, 'data-sh-content');

                if (data) {
                    return '<p>[' + sh_tag + data + ']</p>';
                }
                return match;
            });
        }

        //add popup
        editor_calendar.addCommand('ecwd_popup', function (ui, v) {
            //setup defaults
            var header = '';
            if (v.header)
                header = v.header;
            var footer = '';
            if (v.footer)
                footer = v.footer;


            var page_items = '5';
            if (v.page_items)
                page_items = v.page_items;


            var displays = '';
            var displays_arr = new Array;
            if (v.displays) {
                displays_arr = v.displays.split(",");
            }
            var filters = '';
            if (typeof ecwd_filters !== 'undefined') {
                var filters_arr = new Array;
                if (v.filters) {
                    filters_arr = v.filters.split(",");
                }
                var filters_1 = 'days';
                if (filters_arr[0]) {
                    filters_1 = filters_arr[0];
                }

                var filters_2 = 'categories';
                if (filters_arr[1])
                    filters_2 = filters_arr[1];

                var filters_3 = 'tags';
                if (filters_arr[2])
                    filters_3 = filters_arr[2];

                var filters_4 = 'venues';
                if (filters_arr[3])
                    filters_4 = filters_arr[3];

                var filters_5 = 'organizers';
                if (filters_arr[4])
                    filters_5 = filters_arr[4];

                filters = {
                    title: 'Filters',
                    layout: 'flex',
                    type: "form",
                    direction: 'column',
                    align: 'stretch',
                    items: [
                        {//add id select
                            type: 'listbox',
                            name: 'filters_1',
                            label: 'Filter 1',
                            value: filters_1,
                            'values': ecwd_filters.filters,
                            tooltip: 'Select filter'
                        },
                        {//add id select
                            type: 'listbox',
                            name: 'filters_2',
                            label: 'Filter 2',
                            value: filters_2,
                            'values': ecwd_filters.filters,
                            tooltip: 'Select filter'
                        },
                        {//add id select
                            type: 'listbox',
                            name: 'filters_3',
                            label: 'Filter 3',
                            value: filters_3,
                            'values': ecwd_filters.filters,
                            tooltip: 'Select filter'
                        },
                        {//add id select
                            type: 'listbox',
                            name: 'filters_4',
                            label: 'Filter 4',
                            value: filters_4,
                            'values': ecwd_filters.filters,
                            tooltip: 'Select filter'
                        },
                        {//add id select
                            type: 'listbox',
                            name: 'filters_5',
                            label: 'Filter 5',
                            value: filters_5,
                            'values': ecwd_filters.filters,
                            tooltip: 'Select filter'
                        }
                    ]
                };

            } else {
                filters = {
                    title: 'Filters',
                    layout: 'flex',
                    type: "form",
                    direction: 'column',
                    align: 'stretch',
                    items: [
                        {//add id select
                            type: 'label',
                            text: 'Filter addon should be purchased separately.'

                        }
                    ]
                }
            }


            var display = 'full';
            if (v.display)
                display = v.display;

            var displays_2 = 'list';
            if (displays_arr[1])
                displays_2 = displays_arr[1];

            var displays_3 = 'week';
            if (displays_arr[2])
                displays_3 = displays_arr[2];

            var displays_4 = 'day';
            if (displays_arr[3])
                displays_4 = displays_arr[3];


            var fullchecked = false;
            if (v.fullchecked)
                fullchecked = true;

            var event_search = true;
            if (v.event_search && v.event_search == 'no')
                event_search = false;
            var id = 0;
            var calendars = '';
            console.log(ecwd_plugin.ecwd_calendars.length);
            if (ecwd_plugin.ecwd_calendars.length !== 0) {
                id = ecwd_plugin.ecwd_calendars[0]['value'];
                calendars = {//add id select
                    type: 'listbox',
                    name: 'id',
                    label: 'Select Calendar',
                    value: id,
                    'values': ecwd_plugin.ecwd_calendars,
                    tooltip: 'Select Calendar'
                };
            } else {
                calendars = {//add id select
                    type: 'label',
                    text: 'Please add a calendar before using the shortcode'
                };
            }

            if (v.id)
                id = v.id;
            var content = '';
            if (v.content)
                content = v.content;
            //open the popup
            var win = editor_calendar.windowManager.open({
                title: 'ECWD Shortcode',
                bodyType: 'tabpanel',
                //file : url + '/button_popup.php', // file that contains HTML for our modal window
                body: [
                    {
                        title: 'General',
                        type: "form",
                        layout: 'flex',
                        direction: 'column',
                        align: 'stretch',
                        items: [
                            calendars,

                            {
                                type: 'textbox',
                                name: 'page_items',
                                label: 'Events per page in list view',
                                value: page_items,
                                tooltip: 'Events per page in list view'
                            },
                            {
                                type: 'checkbox',
                                name: 'event_search',
                                label: 'Enable event search',
                                checked: event_search,
                                tooltip: 'Enable event search'
                            }
                        ]
                    },
                    {
                        title: 'Views',
                        type: "form",
                        layout: 'flex',
                        direction: 'column',
                        align: 'stretch',
                        items: [
                            {
                                type: 'listbox',
                                name: 'display',
                                label: 'View 1',
                                value: display,
                                'values': ecwd_plugin.ecwd_views,
                                tooltip: 'Select display mode'
                            },
                            {//add id select
                                type: 'listbox',
                                name: 'displays_2',
                                label: 'View 2',
                                value: displays_2,
                                'values': ecwd_plugin.ecwd_views,
                                tooltip: 'Select display mode'
                            },
                            {//add id select
                                type: 'listbox',
                                name: 'displays_3',
                                label: 'View 3',
                                value: displays_3,
                                'values': ecwd_plugin.ecwd_views,
                                tooltip: 'Select display mode'
                            },
                            {//add id select
                                type: 'listbox',
                                name: 'displays_4',
                                label: 'View 4',
                                value: displays_4,
                                'values': ecwd_plugin.ecwd_views,
                                tooltip: 'Select display mode'
                            },
                            {
                                type: 'label',
                                text: 'Upgrade to Pro version to access three more view options: posterboard, map and 4 days.'
                            }

                        ]
                    },
                    filters

                ],
                onsubmit: function (e) { //when the ok button is clicked
                    var data = win.toJSON();
                    //start the shortcode tag
                    if (typeof data.id != 'undefined' && data.id.length) {
                        var shortcode_str = '[' + sh_tag + ' id="' + data.id + '"';

                        //check for header
                        if (typeof data.header != 'undefined' && data.header.length)
                            shortcode_str += ' header="' + data.header + '"';
                        //check for footer
                        if (typeof data.page_items != 'undefined' && data.page_items.length)
                            shortcode_str += ' page_items="' + data.page_items + '"';
                        if (typeof data.event_search != 'undefined' && data.event_search == true)
                            shortcode_str += ' event_search="yes"';
                        else
                            shortcode_str += ' event_search="no"';

                        if (typeof data.display != 'undefined' && data.display.length)
                            shortcode_str += ' display="' + data.display + '"';
                        shortcode_str += ' displays="';
                        if (typeof data.display != 'undefined' && data.display.length) {
                            shortcode_str += data.display + ',';
                        }
                        if (typeof data.displays_2 != 'undefined' && data.displays_2.length) {
                            shortcode_str += data.displays_2 + ',';
                        }
                        if (typeof data.displays_3 != 'undefined' && data.displays_3.length) {
                            shortcode_str += data.displays_3 + ',';
                        }
                        if (typeof data.displays_4 != 'undefined' && data.displays_4.length) {
                            shortcode_str += data.displays_4;
                        }
                        shortcode_str += '"';

                        shortcode_str += ' filters="';
                        if (typeof data.filters_1 != 'undefined' && data.filters_1.length) {
                            shortcode_str += data.filters_1 + ',';
                        }
                        if (typeof data.filters_2 != 'undefined' && data.filters_2.length) {
                            shortcode_str += data.filters_2 + ',';
                        }
                        if (typeof data.filters_3 != 'undefined' && data.filters_3.length) {
                            shortcode_str += data.filters_3 + ',';
                        }
                        if (typeof data.filters_4 != 'undefined' && data.filters_4.length) {
                            shortcode_str += data.filters_4 + ',';
                        }
                        if (typeof data.filters_5 != 'undefined' && data.filters_5.length) {
                            shortcode_str += data.filters_5;
                        }
                        shortcode_str += '"';
                        shortcode_str += ']';
                        //insert shortcode to tinymce
                        editor_calendar.insertContent(shortcode_str);
                    }
                }
            });
        });

        //add button
        editor_calendar.addButton('ecwd', {
            icon: 'ecwd_calendar',
            tooltip: 'Insert Calendar',
            onclick: function () {
                editor_calendar.execCommand('ecwd_popup', '', {
                    header: '',
                    footer: '',
                    page_items: '5',
                    event_search: 'yes',
                    display: 'full',
                    displays: '',
                    filters: '',
                    id: 0
                });
            }
        });

        //replace from shortcode to an image placeholder
        editor_calendar.on('BeforeSetcontent', function (event) {
            event.content = replaceShortcodes(event.content);
        });

        //replace from image placeholder to shortcode
        editor_calendar.on('GetContent', function (event) {
            event.content = restoreShortcodes(event.content);
        });

        //open popup on placeholder double click
        editor_calendar.on('DblClick', function (e) {
            var cls = e.target.className.indexOf('wp-ecwd-calendar');
            if (e.target.nodeName == 'IMG' && e.target.className.indexOf('wp-ecwd-calendar') > -1) {
                var title = e.target.attributes['data-sh-attr'].value;
                title = window.decodeURIComponent(title);
                editor_calendar.execCommand('ecwd_popup', '', {
                    header: getAttr(title, 'header'),
                    footer: getAttr(title, 'footer'),
                    page_items: getAttr(title, 'page_items'),
                    event_search: getAttr(title, 'event_search'),
                    display: getAttr(title, 'display'),
                    displays: getAttr(title, 'displays'),
                    filters: getAttr(title, 'filters'),
                    id: getAttr(title, 'id')
                });
            }
        });
    });
})();

