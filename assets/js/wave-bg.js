document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.createElement('canvas');
    canvas.id = 'directionalWaveCanvas';
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100vw';
    canvas.style.height = '100vh';
    canvas.style.zIndex = '-1'; // Behind everything
    canvas.style.pointerEvents = 'none';
    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    let width, height;

    function resize() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    const waves = [];
    let lastX = null, lastY = null;

    class DirectionalWave {
        constructor(x, y, vx, vy, speed) {
            this.x = x;
            this.y = y;
            this.vx = vx * Math.min(speed * 0.2, 5); // travel forward based on speed limit
            this.vy = vy * Math.min(speed * 0.2, 5);
            this.life = 1;
            this.maxLife = 40 + Math.min(speed, 50); // longer life for faster swipes
            this.angle = Math.atan2(vy, vx);
            this.size = 5 + Math.min(speed * 0.5, 20); // Initial size arc
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;
            this.size += 1.5; // Expands sideways as it travels forward (bow wave)
            this.vx *= 0.94; // friction
            this.vy *= 0.94;
            this.life++;
        }

        draw(ctx) {
            let progress = this.life / this.maxLife;
            let alpha = 1 - Math.pow(progress, 1.5); // fade out
            if (alpha < 0) alpha = 0;

            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.angle);

            // Draw a curved arc pointing forward
            ctx.beginPath();
            ctx.arc(0, 0, this.size, -Math.PI / 2.5, Math.PI / 2.5);
            ctx.lineWidth = 2 + (1 - progress) * 2;
            ctx.strokeStyle = `rgba(255, 255, 255, ${alpha * 0.8})`; // White foam outer
            ctx.stroke();

            // Inner neon cyan outline for depth
            ctx.beginPath();
            ctx.arc(0, 0, this.size - 1.5, -Math.PI / 2.5, Math.PI / 2.5);
            ctx.lineWidth = 1;
            ctx.strokeStyle = `rgba(0, 210, 255, ${alpha})`; // Neon blue
            ctx.stroke();

            ctx.restore();
        }
    }

    document.addEventListener('mousemove', (e) => {
        if (document.documentElement.classList.contains('dark')) return;

        let mouseX = e.clientX;
        let mouseY = e.clientY;

        if (lastX !== null && lastY !== null) {
            let dx = mouseX - lastX;
            let dy = mouseY - lastY;
            let speed = Math.sqrt(dx * dx + dy * dy);

            // Only spawn wave if moving fast enough to "disturb" the water
            if (speed > 5) {
                // Normalize velocity direction
                let vx = dx / speed;
                let vy = dy / speed;
                // Add a wave
                waves.push(new DirectionalWave(mouseX, mouseY, vx, vy, speed));
            }
        }

        lastX = mouseX;
        lastY = mouseY;
    });

    function animate() {
        requestAnimationFrame(animate);

        if (document.documentElement.classList.contains('dark')) {
            ctx.clearRect(0, 0, width, height);
            canvas.style.display = 'none';
            return;
        } else {
            canvas.style.display = 'block';
        }

        // Clear canvas with transparent background so CSS gradient shows through
        ctx.clearRect(0, 0, width, height);

        for (let i = waves.length - 1; i >= 0; i--) {
            let w = waves[i];
            w.update();
            w.draw(ctx);
            if (w.life >= w.maxLife) {
                waves.splice(i, 1);
            }
        }
    }

    animate();
});
