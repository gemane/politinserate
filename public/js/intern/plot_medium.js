//<![CDATA[
dojo.require("dojo.cache");
dojo.require("dojox.charting.Chart2D");
dojo.require("dojox.charting.widget.Legend");
dojo.require("dojox.charting.action2d.Tooltip");

dojo.addOnLoad(function() {
    var chart_medium = new dojox.charting.Chart2D("chartMedium");
    chart_medium.addPlot("default", {
        type: "Columns",
        tension: 3,
        gap: 10
    }).addAxis("x", {
        fixLower: "major",
        majorTickStep: 1,
        natural: true,
        labels: dataLabelMedium,
        font: "normal normal bold 12pt Helvetica",
    }).addAxis("y", {
        htmlLabels: false,
        vertical: true,
        fixLower: "major",
        fixUpper: "major",
        font: "normal normal bold 12pt Helvetica",
    }).
    addPlot("grid", { type: "Grid" });
    
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
    
    var chart_legend = new dojox.charting.widget.Legend({chart: chart_medium}, "legendMedium");
});
//]]>