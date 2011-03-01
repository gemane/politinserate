//<![CDATA[
dojo.require("dojox.charting.Chart2D");
dojo.require("dojox.charting.widget.Legend");
dojo.require("dojox.charting.action2d.Tooltip");

dojo.addOnLoad(function() {
    var chart_party = new dojox.charting.Chart2D("chartParty");
    chart_party.addPlot("default", {
        type: "StackedColumns",
        tension: 3,
        gap: 10,
    }).addAxis("x", {
        fixLower: "major",
        majorTickStep: 1,
        natural: true,
        labels: dataLabelParty,
        font: "normal normal bold 12pt Helvetica",
    }).addAxis("y", {
        htmlLabels: false,
        vertical: true,
        min: 0,
        max: yaxis,
        fixLower: "major",
        fixUpper: "major",
        font: "normal normal bold 12pt Helvetica",
    }).
    addPlot("grid", { type: "Grid" });
    
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
    
    var chart_legend = new dojox.charting.widget.Legend({chart: chart_party}, "legendParty");
});
//]]>