<script setup>
import { ease, Root, color, Tooltip, Template, Container, Circle, Picture, p50, Bullet } from "@amcharts/amcharts5";
import { XYChart, CategoryAxis, AxisRendererX, AxisRendererY, ValueAxis, ColumnSeries, XYCursor } from "@amcharts/amcharts5/xy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
import * as am5plugins_exporting from "@amcharts/amcharts5/plugins/exporting";

import { onMounted } from 'vue'

const am5 = {
    ease: ease,
    Root: Root,
    color: color,
    Tooltip: Tooltip,
    Template: Template,
    Container: Container,
    Circle: Circle,
    Picture: Picture,
    p50: p50,
    Bullet: Bullet,
}

const am5xy = {
    XYChart: XYChart,
    CategoryAxis: CategoryAxis,
    AxisRendererX: AxisRendererX,
    AxisRendererY: AxisRendererY,
    ValueAxis: ValueAxis,
    ColumnSeries: ColumnSeries,
    XYCursor: XYCursor,
}

let currentlyHovered;

function handleHover(dataItem) {
    if (dataItem && currentlyHovered != dataItem) {
        handleOut();
        currentlyHovered = dataItem;
        let bullet = dataItem.bullets[0];
        bullet.animate({
            key: "locationY",
            to: 1,
            duration: 600,
            easing: am5.ease.out(am5.ease.cubic)
        });
    }
}

function handleOut() {
    if (currentlyHovered) {
        let bullet = currentlyHovered.bullets[0];
        bullet.animate({
            key: "locationY",
            to: 0,
            duration: 600,
            easing: am5.ease.out(am5.ease.cubic)
        });
    }
}

onMounted(() => {
    /* Chart code */
    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    let root = am5.Root.new("more-sales-chart");

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
        am5themes_Animated.new(root)
    ]);

    root.interfaceColors.set("text", am5.color(0xffffff));

    let data = [
        {
            name: "1",
            steps: 365,
            pictureSettings: {
                src: "/src/items/ajiaco-cubano-40174.png"
            }
        },
        {
            name: "2",
            steps: 548,
            pictureSettings: {
                src: "/src/items/arroz-belicenho-58335.png"
            }
        },
        {
            name: "3",
            steps: 368,
            pictureSettings: {
                src: "/src/items/big-mac-25035.png"
            }
        },
        {
            name: "4",
            steps: 485,
            pictureSettings: {
                src: "/src/items/bizcocho-94621.png"
            }
        },
        {
            name: "5",
            steps: 980,
            pictureSettings: {
                src: "/src/items/choto-al-ajillo-47845.png"
            }
        },
        {
            name: "6",
            steps: 236,
            pictureSettings: {
                src: "/src/items/croquetas-de-pollo-7850.png"
            }
        },
        {
            name: "7",
            steps: 354,
            pictureSettings: {
                src: "/src/items/hamburguesa-simple-88233.png"
            }
        },
        {
            name: "8",
            steps: 654,
            pictureSettings: {
                src: "/src/items/papas-fritas-bote-16625.png"
            }
        }
    ];

    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            wheelX: "none",
            wheelY: "none",
            paddingBottom: 50,
            paddingTop: 40,
            paddingLeft: 0,
            paddingRight: 0
        })
    );

    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/

    let xRenderer = am5xy.AxisRendererX.new(root, {
        minorGridEnabled: true,
        minGridDistance: 60
    });
    xRenderer.grid.template.set("visible", false);

    let xAxis = chart.xAxes.push(
        am5xy.CategoryAxis.new(root, {
            paddingTop: 40,
            categoryField: "name",
            renderer: xRenderer
        })
    );


    let yRenderer = am5xy.AxisRendererY.new(root, {});
    yRenderer.grid.template.set("strokeDasharray", [3]);

    let yAxis = chart.yAxes.push(
        am5xy.ValueAxis.new(root, {
            min: 0,
            renderer: yRenderer
        })
    );

    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    let series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
            name: "Income",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "steps",
            categoryXField: "name",
            sequencedInterpolation: true,
            calculateAggregates: true,
            maskBullets: false,
            tooltip: am5.Tooltip.new(root, {
                dy: -30,
                pointerOrientation: "vertical",
                labelText: "Número de ventas: {valueY}"
            })
        })
    );

    series.columns.template.setAll({
        strokeOpacity: 0,
        cornerRadiusBR: 10,
        cornerRadiusTR: 10,
        cornerRadiusBL: 10,
        cornerRadiusTL: 10,
        maxWidth: 40,
        fillOpacity: 0.8
    });

    series.columns.template.events.on("pointerover", function (e) {
        handleHover(e.target.dataItem);
    });

    series.columns.template.events.on("pointerout", function (e) {
        handleOut();
    });

    let circleTemplate = am5.Template.new({});

    series.bullets.push(function (root, series, dataItem) {
        let bulletContainer = am5.Container.new(root, {});
        let circle = bulletContainer.children.push(
            am5.Circle.new(
                root,
                {
                    radius: 28
                },
                circleTemplate
            )
        );

        let maskCircle = bulletContainer.children.push(
            am5.Circle.new(root, { radius: 23 })
        );

        // only containers can be masked, so we add image to another container
        let imageContainer = bulletContainer.children.push(
            am5.Container.new(root, {
                mask: maskCircle
            })
        );

        let image = imageContainer.children.push(
            am5.Picture.new(root, {
                templateField: "pictureSettings",
                centerX: am5.p50,
                centerY: am5.p50,
                width: 50,
                height: 50
            })
        );

        return am5.Bullet.new(root, {
            locationY: 0,
            sprite: bulletContainer
        });
    });

    // heatrule
    series.set("heatRules", [
        {
            dataField: "valueY",
            min: am5.color(0xe5dc36),
            max: am5.color(0xe55100),
            target: series.columns.template,
            key: "fill"
        },
        {
            dataField: "valueY",
            min: am5.color(0xe5dc36),
            max: am5.color(0xe55100),
            target: circleTemplate,
            key: "fill"
        }
    ]);

    series.data.setAll(data);
    xAxis.data.setAll(data);

    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
    cursor.lineX.set("visible", false);
    cursor.lineY.set("visible", false);

    cursor.events.on("cursormoved", function () {
        let dataItem = series.get("tooltip").dataItem;
        if (dataItem) {
            handleHover(dataItem);
        } else {
            handleOut();
        }
    });

    let exporting = am5plugins_exporting.Exporting.new(root, {
        menu: am5plugins_exporting.ExportingMenu.new(root, {})
    });

    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    series.appear();
    chart.appear(1000, 100);
})
</script>
<template>
    <div class="w-full h-[400px]" id="more-sales-chart">

    </div>
</template>
