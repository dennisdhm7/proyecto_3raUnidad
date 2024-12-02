<script setup>
import { ease, Root, color, Tooltip, DataProcessor, Scrollbar } from "@amcharts/amcharts5";
import { XYChart, DateAxis, AxisRendererX, ValueAxis, AxisRendererY, SmoothedXLineSeries, XYCursor } from "@amcharts/amcharts5/xy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
import * as am5plugins_exporting from "@amcharts/amcharts5/plugins/exporting";

import { onMounted } from 'vue'

const am5 = {
    ease: ease,
    Root: Root,
    color: color,
    Tooltip: Tooltip,
    DataProcessor: DataProcessor,
    Scrollbar: Scrollbar,
}

const am5xy = {
    XYChart: XYChart,
    DateAxis: DateAxis,
    AxisRendererX: AxisRendererX,
    ValueAxis: ValueAxis,
    AxisRendererY: AxisRendererY,
    SmoothedXLineSeries: SmoothedXLineSeries,
    XYCursor: XYCursor,
}

onMounted(() => {
    /* Chart code */
    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    let root = am5.Root.new("reservations-chart");


    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
        am5themes_Animated.new(root)
    ]);

    root.interfaceColors.set("text", am5.color(0xffffff));

    root.dateFormatter.setAll({
        dateFormat: "yyyy",
        dateFields: ["valueX"]
    });

    let data = []
    for (let i = 0; ; i++) {
        const date = new Date(2024, 7, 15);
        date.setDate(Number(date.getDate()) + Number(i))

        data.push({
            date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
            value: Math.ceil(Math.random() * (100 - 1) + 1)
        })

        if (date.valueOf() >= (new Date().valueOf() - 86400000)) {
            break
        }
    }


    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    let chart = root.container.children.push(am5xy.XYChart.new(root, {
        focusable: true,
        panX: true,
        panY: true,
        wheelX: "panX",
        wheelY: "zoomX",
        pinchZoomX: true,
        paddingLeft: 0
    }));

    let easing = am5.ease.linear;


    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    let xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
        start: 1.75,
        maxDeviation: 0.5,
        groupData: false,
        baseInterval: {
            timeUnit: "day",
            count: 1
        },
        renderer: am5xy.AxisRendererX.new(root, {
            minorGridEnabled: true,
            minGridDistance: 70
        }),
        tooltip: am5.Tooltip.new(root, {})
    }));

    let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        maxDeviation: 0.2,
        renderer: am5xy.AxisRendererY.new(root, {})
    }));


    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    let series = chart.series.push(am5xy.SmoothedXLineSeries.new(root, {
        minBulletDistance: 10,
        connect: false,
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "value",
        valueXField: "date",
        categoryXField: "category",
        tooltip: am5.Tooltip.new(root, {
            pointerOrientation: "horizontal",
            labelText: "{valueY}"
        })
    }));

    series.fills.template.setAll({
        fillOpacity: 0.2,
        visible: true
    });

    series.strokes.template.setAll({
        strokeWidth: 2
    });


    // Set up data processor to parse string dates
    // https://www.amcharts.com/docs/v5/concepts/data/#Pre_processing_data
    series.data.processor = am5.DataProcessor.new(root, {
        dateFormat: "yyyy-MM-dd",
        dateFields: ["date"]
    });

    series.data.setAll(data);

    series.set("fill", am5.color(0xe55100));
    series.set("stroke", am5.color(0xe55100));

    // Add cursor
    // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
        xAxis: xAxis,
        behavior: "none"
    }));
    cursor.lineY.set("visible", false);

    // add scrollbar
    chart.set("scrollbarX", am5.Scrollbar.new(root, {
        orientation: "horizontal"
    }));

    let exporting = am5plugins_exporting.Exporting.new(root, {
        menu: am5plugins_exporting.ExportingMenu.new(root, {})
    });

    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    chart.appear(1000, 100);
})
</script>
<template>
    <div class="w-full h-[400px]" id="reservations-chart">

    </div>
</template>
