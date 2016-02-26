if (!window.ATK) {
    var ATK = {};
}

ATK.Attribute = {
    /**
     * Refresh the attribute input form using Ajax.
     */
    callDependencies: function(url, el) {
        var form = null, pn = el.parentNode;

        // Loop trough the previous nodes to find the parent form element of our
        // element. We stop searching when we reached the body element. 
        while (pn.tagName !== 'body' && pn.tagName !== 'BODY') {
            if (pn.tagName === 'form' || pn.tagName === 'FORM') {
                form = pn;
                break;
            } else {
                pn = pn.parentNode;
            }
        }

        if (form == null)
            return;

        var elements = Form.getElements(form);
        var queryComponents = new Array();

        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name && elements[i].name.substring(0, 3) != 'atk') {
                if (elements[i].className == 'shuttle_select') {
                    if (elements[i].name.substring(elements[i].name.length - 4) != '_sel') {
                        var queryComponent = this.serializeShuttle(elements[i]);
                    } else {
                        var queryComponent = null;
                    }
                } else {
                    var queryComponent = Form.Element.serialize(elements[i]);
                }
                if (queryComponent)
                    queryComponents.push(queryComponent);
            }
        }

        atkErrorFields.each(function(field) {
            var queryComponent = $H({'atkerrorfields[]': field
            }).toQueryString();
            queryComponents.push(queryComponent);
        });

        var params = queryComponents.join('&');

        var func = function(transport) {
            transport.responseText.evalScripts();
        };

        new Ajax.Request(url, {method: 'post', parameters: params,
            evalScripts: true, onComplete: func});
    },
    /**
     * Refresh the attribute input form using Ajax.
     */
    refresh: function(url, focusFirstFormEl) {
        var form = 'entryform';

        var elements = Form.getElements(form);
        var queryComponents = new Array();

        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name && elements[i].name.substring(0, 3) != 'atk') {
                if (elements[i].className == 'shuttle_select') {
                    if (elements[i].name.substring(elements[i].name.length - 4) != '_sel') {
                        var queryComponent = this.serializeShuttle(elements[i]);
                    } else {
                        var queryComponent = null;
                    }
                } else {
                    var queryComponent = Form.Element.serialize(elements[i]);
                }
                if (queryComponent)
                    queryComponents.push(queryComponent);
            }
        }

        atkErrorFields.each(function(field) {
            var queryComponent = $H({'atkerrorfields[]': field
            }).toQueryString();
            queryComponents.push(queryComponent);
        });

        var params = queryComponents.join('&');

        var func = function(transport) {
            transport.responseText.evalScripts();
        };
        if (focusFirstFormEl) {
            func = function(transport) {
                transport.responseText.evalScripts();
            };
        }

        new Ajax.Request(url, {method: 'post', parameters: params,
            evalScripts: true, onComplete: func});
    },
    serializeShuttle: function(element) {
        var values, length = element.length;
        if (!length)
            return null;

        for (var i = 0, values = []; i < length; i++) {
            var opt = element.options[i];
            values.push(Form.Element.Serializers.optionValue(opt));
        }
        var pair = {};
        pair[element.name] = values;
        return Object.toQueryString(pair);
    },
    refreshDisplay: function(url) {
        var func = function(transport) {
            transport.responseText.evalScripts();
        };
        new Ajax.Request(url, {method: 'post', evalScripts: true,
            onComplete: func});
    }
};