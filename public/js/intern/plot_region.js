//<![CDATA[
dojo.require("dojo.cache");
dojo.require("dojox.charting.Chart2D");
dojo.require("dojox.charting.widget.Legend");
dojo.require("dojox.charting.action2d.Tooltip");

dojo.addOnLoad(function() {
    var chart_region = new dojox.charting.Chart2D("chartRegion");
    chart_region.addPlot("default", {
        type: "Columns",
        tension: 3,
        gap: 10
    }).addAxis("x", {
        fixLower: "major",
        majorTickStep: 1,
        natural: true,
        labels: dataLabelRegion,
        font: "normal normal bold 12pt Helvetica",
    }).addAxis("y", {
        htmlLabels: false,
        vertical: true,
        fixLower: "major",
        fixUpper: "major",
        font: "normal normal bold 12pt Helvetica",
    }).
    addPlot("grid", { type: "Grid" });
    
    len = dataPaymentsRegion.length;
    for (var i = 0; i < len; i++) {
        if (dataPaymentsRegion[i] != 0) {
            dataPaymentRegion = new Array(len-1);
            for (var j = 0; j < len; j++) {
                if (i == j) {
                    dataPaymentRegion[j] = new Object();
                    dataPaymentRegion[j]["y"] = dataPaymentsRegion[i];
                    dataPaymentRegion[j]["tooltip"] = dataLegendRegion[i] + ": " + dataPaymentsRegion[i] + " EUR";
                } else {
                    dataPaymentRegion[j] = new Object();
                    dataPaymentRegion[j]["y"] = 0;
                }
            }
            chart_region.addSeries(dataLegendRegion[i], dataPaymentRegion, {stroke: "black", fill: dataColorsRegion[i]});
        }
    }
    
    var chart_tooltip = new dojox.charting.action2d.Tooltip(chart_region, "default");
    chart_region.render();
    
    var chart_legend = new dojox.charting.widget.Legend({chart: chart_region}, "legendRegion");
});
//]]>