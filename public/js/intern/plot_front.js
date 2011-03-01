//<![CDATA[
dojo.require("dojo.cache");
dojo.require("dojox.charting.Chart2D");
dojo.require("dojox.charting.action2d.Tooltip");

dojo.addOnLoad(function() {
    var chart_medium = new dojox.charting.Chart2D("chartMedium");
    chart_medium.addPlot("default", {
        type: "Columns",
        tension: 3,
        gap: 3
    }).addAxis("x", {
        fixLower: "major",
        majorTickStep: 1,
        natural: true,
        labels: dataLabelMedium
    }).addAxis("y", {
        htmlLabels: false,
        vertical: true,
        fixLower: "major",
        fixUpper: "major",
    });
    
    len = dataPaymentsMedium.length;
    for (var i = 0; i < len; i++) {
        if (dataPaymentsMedium[i] != 0) {
            dataPaymentMedium = new Array(len-1);
            for (var j = 0; j < len; j++) {
                if (i == j) {
                    dataPaymentMedium[j] = new Object();
                    dataPaymentMedium[j]["y"] = dataPaymentsMedium[i];
                    dataPaymentMedium[j]["tooltip"] = dataLegendMedium[i] + ": " + dataPaymentsMedium[i] + " EUR";
                } else {
                    dataPaymentMedium[j] = new Object();
                    dataPaymentMedium[j]["y"] = 0;
                }
            }
            chart_medium.addSeries(dataLegendMedium[i], dataPaymentMedium, {stroke: "black", fill: dataColorsMedium[i]});
        }
    }
    
    var chart_tooltip = new dojox.charting.action2d.Tooltip(chart_medium, "default");
    chart_medium.render();
    
});

dojo.addOnLoad(function() {
    var chart_party = new dojox.charting.Chart2D("chartParty");
    chart_party.addPlot("default", {
        type: "StackedColumns",
        tension: 3,
        gap: 3
    }).addAxis("x", {
        fixLower: "major",
        natural: true,
        labels: dataLabelParty
    }).addAxis("y", {
        htmlLabels: false,
        vertical: true,
        min: 0,
        max: yaxis, // TODO2 Seems to be a bug -> calculate max yaxis
        fixLower: "major",
        fixUpper: "major",
    });
    
    len = dataPaymentsParty.length;
    for (var i = 0; i < len; i++) {
        if (dataPaymentsParty[i] != 0) {
            dataPaymentParty = new Array(len-1);
            for (var j = 0; j < len; j++) {
                if (i == j) {
                    dataPaymentParty[j] = new Object();
                    dataPaymentParty[j]["y"] = dataPaymentsParty[i];
                    dataPaymentParty[j]["tooltip"] = dataLegendParty[i] + ": " + dataPaymentsParty[i] + " EUR";
                } else {
                    dataPaymentParty[j] = new Object();
                    dataPaymentParty[j]["y"] = 0;
                }
            }
            chart_party.addSeries(dataLegendParty[i], dataPaymentParty, {stroke: "black", fill: dataColorsParty[i]});
        }
        
        if (dataPaymentsGovernment[i] != 0) {
            dataPaymentGovernment = new Array(len-1);
            for (var j = 0; j < len; j++) {
                if (i == j) {
                    dataPaymentGovernment[j] = new Object();
                    dataPaymentGovernment[j]["y"] = dataPaymentsGovernment[i];
                    dataPaymentGovernment[j]["tooltip"] = dataLegendGovernment[i] + ": " + dataPaymentsGovernment[i] + " EUR";
                } else {
                    dataPaymentGovernment[j] = new Object();
                    dataPaymentGovernment[j]["y"] = 0;
                }
            }
            chart_party.addSeries(dataLegendGovernment[i], dataPaymentGovernment, {stroke: "black", fill: dataColorsGovernment[i]});
        }
    }
    
    var chart_tooltip = new dojox.charting.action2d.Tooltip(chart_party, "default");
    chart_party.render();
    
});
//]]>