import * as echarts from 'echarts/core';
import { LineChart, BarChart, PieChart } from 'echarts/charts';
import {
    GridComponent,
    TooltipComponent,
    LegendComponent,
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';

echarts.use([
    LineChart,
    BarChart,
    PieChart,
    GridComponent,
    TooltipComponent,
    LegendComponent,
    CanvasRenderer,
]);

function readChartsConfig() {
    const jsonEl = document.getElementById('dashboard-charts-json');
    if (!jsonEl?.textContent) {
        return [];
    }
    try {
        const data = JSON.parse(jsonEl.textContent.trim());
        return Array.isArray(data) ? data : [];
    } catch {
        return [];
    }
}

function initDashboardCharts() {
    const charts = readChartsConfig();
    if (charts.length === 0) {
        return;
    }

    const instances = [];

    charts.forEach((chart) => {
        const mount = document.getElementById(chart.id);
        if (!mount || mount.dataset.echartsInit === '1') {
            return;
        }
        mount.dataset.echartsInit = '1';

        const height = chart.height || 300;
        mount.style.height = `${height}px`;

        const instance = echarts.init(mount, null, { renderer: 'canvas' });
        instance.setOption(chart.option || {});
        instances.push(instance);
    });

    if (instances.length === 0) {
        return;
    }

    const resize = () => {
        instances.forEach((inst) => inst.resize());
    };

    window.addEventListener('resize', resize, { passive: true });

    if (typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(() => resize());
        instances.forEach((inst) => {
            const el = inst.getDom()?.parentElement;
            if (el) {
                ro.observe(el);
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
} else {
    initDashboardCharts();
}
