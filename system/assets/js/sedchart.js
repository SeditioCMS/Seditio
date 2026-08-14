/**
 * SedChart - Lightweight Canvas Line Chart (~2.5 KB) for Seditio CMS
 * (c) Seditio CMS
 */
(function (window, document) {
    'use strict';

    function SedChart(container, options) {
        if (typeof container === 'string') {
            container = document.getElementById(container);
        }
        if (!container) return;

        this.container = container;
        this.options = options || {};
        this.labels = options.labels || [];
        this.values = options.values || [];

        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.container.innerHTML = '';
        this.container.appendChild(this.canvas);

        this.hoverIndex = -1;
        this.points = [];

        this.init();
    }

    SedChart.prototype.init = function () {
        var self = this;

        this.resize();
        this.bindEvents();
        this.render();

        window.addEventListener('resize', function () {
            self.resize();
            self.render();
        });
    };

    SedChart.prototype.resize = function () {
        var parent = this.container.parentNode || this.container;
        var parentRect = parent.getBoundingClientRect();
        var dpr = window.devicePixelRatio || 1;

        this.width = parentRect.width || 600;
        this.height = parentRect.height > 50 ? parentRect.height : 350;

        this.canvas.width = this.width * dpr;
        this.canvas.height = this.height * dpr;
        this.canvas.style.width = this.width + 'px';
        this.canvas.style.height = this.height + 'px';
        this.ctx.scale(dpr, dpr);
    };

    SedChart.prototype.setData = function (labels, values) {
        this.labels = labels || [];
        this.values = values || [];
        this.hoverIndex = -1;
        this.render();
    };

    SedChart.prototype.render = function () {
        var ctx = this.ctx;
        var w = this.width;
        var h = this.height;
        var padding = { top: 25, right: 30, bottom: 50, left: 70 };

        ctx.clearRect(0, 0, w, h);

        if (!this.values || this.values.length === 0) return;

        var chartW = w - padding.left - padding.right;
        var chartH = h - padding.top - padding.bottom;

        // Find Y max
        var maxVal = Math.max.apply(null, this.values);
        if (maxVal === 0) maxVal = 10;
        maxVal = Math.ceil(maxVal * 1.15); // Add headroom

        // Draw Y Axis Grid & Labels
        var steps = 5;
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        ctx.font = '12px system-ui, -apple-system, sans-serif';
        ctx.fillStyle = '#718096';
        ctx.strokeStyle = '#edf2f7';
        ctx.lineWidth = 1;

        for (var i = 0; i <= steps; i++) {
            var yVal = Math.round((maxVal / steps) * i);
            var yPos = padding.top + chartH - (i / steps) * chartH;

            // Grid Line
            ctx.beginPath();
            ctx.moveTo(padding.left, yPos);
            ctx.lineTo(w - padding.right, yPos);
            ctx.stroke();

            // Label
            ctx.fillText(yVal.toLocaleString(), padding.left - 12, yPos);
        }

        // Calculate Points
        var len = this.values.length;
        var stepX = len > 1 ? chartW / (len - 1) : chartW / 2;
        this.points = [];

        for (var k = 0; k < len; k++) {
            var px = padding.left + (len === 1 ? chartW / 2 : k * stepX);
            var py = padding.top + chartH - (this.values[k] / maxVal) * chartH;
            this.points.push({ x: px, y: py, val: this.values[k], label: this.labels[k] });
        }

        // Draw X Axis Labels
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        // Limit X labels density according to width
        var maxLabels = Math.max(2, Math.floor(chartW / 85));
        var labelStep = Math.ceil(len / maxLabels);

        for (var m = 0; m < len; m += labelStep) {
            ctx.fillText(this.labels[m] || '', this.points[m].x, padding.top + chartH + 12);
        }

        // Draw Area Gradient & Smooth Line
        if (this.points.length > 0) {
            // Fill Path
            ctx.beginPath();
            ctx.moveTo(this.points[0].x, padding.top + chartH);
            ctx.lineTo(this.points[0].x, this.points[0].y);

            for (var p = 0; p < this.points.length - 1; p++) {
                var p1 = this.points[p];
                var p2 = this.points[p + 1];
                var cpx = (p1.x + p2.x) / 2;
                ctx.bezierCurveTo(cpx, p1.y, cpx, p2.y, p2.x, p2.y);
            }

            ctx.lineTo(this.points[this.points.length - 1].x, padding.top + chartH);
            ctx.closePath();

            var gradient = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartH);
            gradient.addColorStop(0, 'rgba(87, 160, 0, 0.30)');
            gradient.addColorStop(1, 'rgba(87, 160, 0, 0.0)');
            ctx.fillStyle = gradient;
            ctx.fill();

            // Stroke Line
            ctx.beginPath();
            ctx.moveTo(this.points[0].x, this.points[0].y);

            for (var s = 0; s < this.points.length - 1; s++) {
                var s1 = this.points[s];
                var s2 = this.points[s + 1];
                var scpx = (s1.x + s2.x) / 2;
                ctx.bezierCurveTo(scpx, s1.y, scpx, s2.y, s2.x, s2.y);
            }

            ctx.strokeStyle = '#57a000';
            ctx.lineWidth = 3;
            ctx.stroke();

            // Draw Data Points
            for (var pt = 0; pt < this.points.length; pt++) {
                var ptObj = this.points[pt];
                var isHover = (pt === this.hoverIndex);

                ctx.beginPath();
                ctx.arc(ptObj.x, ptObj.y, isHover ? 6 : 4, 0, Math.PI * 2);
                ctx.fillStyle = isHover ? '#ffffff' : '#57a000';
                ctx.fill();
                ctx.strokeStyle = '#57a000';
                ctx.lineWidth = isHover ? 3 : 2;
                ctx.stroke();
            }
        }

        // Draw Hover Line & Tooltip
        if (this.hoverIndex >= 0 && this.hoverIndex < this.points.length) {
            var active = this.points[this.hoverIndex];

            // Vertical guide line
            ctx.beginPath();
            ctx.setLineDash([4, 4]);
            ctx.moveTo(active.x, padding.top);
            ctx.lineTo(active.x, padding.top + chartH);
            ctx.strokeStyle = '#a0aec0';
            ctx.lineWidth = 1;
            ctx.stroke();
            ctx.setLineDash([]);

            // Draw Tooltip Box
            var title = active.label || '';
            var valStr = (this.options.seriesLabel || 'Hits') + ': ' + active.val.toLocaleString();
            ctx.font = 'bold 13px system-ui, -apple-system, sans-serif';
            var titleWidth = ctx.measureText(title).width;
            var valWidth = ctx.measureText(valStr).width;
            var boxW = Math.max(titleWidth, valWidth) + 24;
            var boxH = 50;

            var boxX = active.x - boxW / 2;
            var boxY = active.y - boxH - 12;

            if (boxX < 10) boxX = 10;
            if (boxX + boxW > w - 10) boxX = w - boxW - 10;
            if (boxY < 10) boxY = active.y + 16;

            // Box shadow & background
            ctx.fillStyle = 'rgba(26, 32, 44, 0.95)';
            ctx.beginPath();
            if (typeof ctx.roundRect === 'function') {
                ctx.roundRect(boxX, boxY, boxW, boxH, 6);
            } else {
                ctx.rect(boxX, boxY, boxW, boxH);
            }
            ctx.fill();

            // Text
            ctx.textAlign = 'left';
            ctx.textBaseline = 'top';
            ctx.fillStyle = '#a0aec0';
            ctx.font = '12px system-ui, -apple-system, sans-serif';
            ctx.fillText(title, boxX + 12, boxY + 8);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 13px system-ui, -apple-system, sans-serif';
            ctx.fillText(valStr, boxX + 12, boxY + 26);
        }
    };

    SedChart.prototype.bindEvents = function () {
        var self = this;

        this.canvas.addEventListener('mousemove', function (e) {
            var rect = self.canvas.getBoundingClientRect();
            var mouseX = e.clientX - rect.left;
            var closestIdx = -1;
            var minDist = Infinity;

            for (var i = 0; i < self.points.length; i++) {
                var dist = Math.abs(self.points[i].x - mouseX);
                if (dist < minDist) {
                    minDist = dist;
                    closestIdx = i;
                }
            }

            if (closestIdx !== self.hoverIndex) {
                self.hoverIndex = closestIdx;
                self.render();
            }
        });

        this.canvas.addEventListener('mouseleave', function () {
            if (self.hoverIndex !== -1) {
                self.hoverIndex = -1;
                self.render();
            }
        });
    };

    window.SedChart = SedChart;
})(window, document);
