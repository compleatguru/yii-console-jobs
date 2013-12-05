
function populatePrefectureCityList(data) {
    var select = new HTML5Select('#prefecturecity');
    data = JSON.parse(data);
    var _city = new Array();
    var _prefecture = new Array();
    var result = new Array();
    for (_country in data) {
        _prefecture = new Array();
        for (Prefecture in data[_country]) {
            _city = new Array();
            for (City in data[_country][Prefecture]) {
                _city.push(new option(City, data[_country][Prefecture][City]));
            }
            _prefecture.push(new Soptgroup(Prefecture, _city));
        }
        result.push(new Soptgroup(_country, _prefecture));
    }

    var s = $("<select></select>");
    $.each(result, function(i) {
        var _country = result[i].label;
        var children = result[i].children;
        $.each(children, function(j) {
            var optgroup1 = new $('<optgroup>');
            var inner = children[j].children;
            $.each(inner, function(x) {
                var option = new $("<option></option>");
                option.val(new String(inner[x].id).toLocaleString());
                option.text(new String(inner[x].label).toLocaleString());
                optgroup1.append(option);
            });
            optgroup1.attr('label', new String(_country + ":" + children[j].label).toLocaleString());
            s.append(optgroup1);
        });
//                        s.append(optgroup);
    });
            $("#prefecturecity").empty().html(s.html());
    select.setSelectedOptions();
    $("#prefecturecity").trigger('chosen:updated');
};
