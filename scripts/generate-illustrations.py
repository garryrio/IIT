"""
Generator ilustrasi SVG orisinal untuk data contoh (seed) Marga Mulya Hub.
Semua gambar dibuat dari bentuk geometris sederhana oleh tim (bukan foto/aset pihak ketiga),
sehingga bebas dipakai. Ganti dengan foto asli desa melalui CMS.

Jalankan:  python scripts/generate-illustrations.py
"""
import os
import random

OUT = os.path.join(os.path.dirname(__file__), "..", "public", "images", "seed")
W, H = 1200, 800

PALETTES = {
    "pagi": dict(sky1="#fbe7c6", sky2="#f6f1e4", sun="#f2b134", far="#9cc58a", mid="#5f9e57", near="#2f6b3a", field="#8dbb5a", field2="#a9cf6d"),
    "siang": dict(sky1="#cfe8ef", sky2="#f4f7ef", sun="#f6c94c", far="#a8cf96", mid="#6aa85f", near="#2f6b3a", field="#93c063", field2="#b3d777"),
    "senja": dict(sky1="#f3b07c", sky2="#fbe3c4", sun="#e46f2c", far="#b39a6c", mid="#6f7f45", near="#3b4f2a", field="#a7a14f", field2="#c4b65e"),
    "teduh": dict(sky1="#dfeee0", sky2="#f8f6ee", sun="#efc25a", far="#b6d3a8", mid="#7fae6f", near="#3d7a45", field="#9cc46c", field2="#bad987"),
}


def hills(y, amp, color, seed, opacity=1):
    r = random.Random(seed)
    pts = [f"M0 {y}"]
    x = 0
    while x < W:
        nx = min(W, x + r.randint(180, 320))
        cy = y - r.randint(int(amp * 0.4), amp)
        pts.append(f"Q{(x + nx) / 2:.0f} {cy} {nx} {y + r.randint(-20, 20)}")
        x = nx
    pts.append(f"L{W} {H} L0 {H} Z")
    return f'<path d="{" ".join(pts)}" fill="{color}" opacity="{opacity}"/>'


def rice_field(top, p):
    s = [f'<rect x="0" y="{top}" width="{W}" height="{H - top}" fill="{p["field"]}"/>']
    for i in range(14):
        y = top + (i ** 1.6) * 6
        if y > H:
            break
        s.append(f'<path d="M0 {y:.0f} Q600 {y - 12:.0f} {W} {y:.0f}" stroke="{p["field2"]}" stroke-width="{2 + i * 0.6:.1f}" fill="none" opacity=".9"/>')
    for x in range(-200, W + 200, 90):
        s.append(f'<line x1="{W / 2:.0f}" y1="{top}" x2="{x}" y2="{H}" stroke="{p["near"]}" stroke-width="1.2" opacity=".18"/>')
    return "".join(s)


def tree(x, y, s=1.0, c="#2f6b3a"):
    return (f'<rect x="{x - 6 * s:.0f}" y="{y - 40 * s:.0f}" width="{12 * s:.0f}" height="{42 * s:.0f}" rx="4" fill="#6b4a2b"/>'
            f'<circle cx="{x}" cy="{y - 70 * s:.0f}" r="{42 * s:.0f}" fill="{c}"/>'
            f'<circle cx="{x - 28 * s:.0f}" cy="{y - 52 * s:.0f}" r="{28 * s:.0f}" fill="{c}" opacity=".9"/>'
            f'<circle cx="{x + 30 * s:.0f}" cy="{y - 50 * s:.0f}" r="{26 * s:.0f}" fill="{c}" opacity=".85"/>')


def palm(x, y, s=1.0):
    leaves = "".join(
        f'<path d="M{x} {y - 150 * s:.0f} q{dx * s:.0f} {-30 * s:.0f} {dx * 1.6 * s:.0f} {25 * s:.0f}" stroke="#3d7a45" stroke-width="{10 * s:.0f}" fill="none" stroke-linecap="round"/>'
        for dx in (-70, -40, 40, 70))
    return f'<path d="M{x} {y} q{-10 * s:.0f} {-80 * s:.0f} 0 {-150 * s:.0f}" stroke="#7a5a35" stroke-width="{9 * s:.0f}" fill="none"/>{leaves}'


def house(x, y, w=150, h=95, wall="#f4e6cf", roof="#b5452f"):
    return (f'<rect x="{x}" y="{y - h}" width="{w}" height="{h}" fill="{wall}"/>'
            f'<path d="M{x - 18} {y - h} L{x + w / 2} {y - h - 62} L{x + w + 18} {y - h} Z" fill="{roof}"/>'
            f'<rect x="{x + w * 0.42}" y="{y - h * 0.62}" width="{w * 0.18}" height="{h * 0.62}" fill="#7a4e2c"/>'
            f'<rect x="{x + w * 0.1}" y="{y - h * 0.7}" width="{w * 0.2}" height="{h * 0.25}" fill="#9fc6d6"/>'
            f'<rect x="{x + w * 0.7}" y="{y - h * 0.7}" width="{w * 0.2}" height="{h * 0.25}" fill="#9fc6d6"/>')


def hall(x, y, w=420, h=150, roof="#8a3b26", label_color="#1f4d2b", flag=False):
    pillars = "".join(f'<rect x="{x + 20 + i * (w - 40) / 5:.0f}" y="{y - h}" width="14" height="{h}" fill="#e9dcc3"/>' for i in range(6))
    s = (f'<rect x="{x - 10}" y="{y}" width="{w + 20}" height="18" fill="#cdbb9a"/>'
         f'<rect x="{x}" y="{y - h}" width="{w}" height="{h}" fill="#f7efe0"/>{pillars}'
         f'<path d="M{x - 40} {y - h} L{x + w / 2} {y - h - 110} L{x + w + 40} {y - h} Z" fill="{roof}"/>'
         f'<path d="M{x + w / 2 - 70} {y - h - 20} L{x + w / 2} {y - h - 80} L{x + w / 2 + 70} {y - h - 20} Z" fill="#a24a31"/>'
         f'<rect x="{x + w / 2 - 90}" y="{y - h + 18}" width="180" height="26" rx="4" fill="{label_color}"/>'
         f'<rect x="{x + w / 2 - 34}" y="{y - h * 0.55}" width="68" height="{h * 0.55}" fill="#7a4e2c"/>')
    if flag:
        s += (f'<line x1="{x + w + 90}" y1="{y}" x2="{x + w + 90}" y2="{y - 260}" stroke="#777" stroke-width="5"/>'
              f'<rect x="{x + w + 92}" y="{y - 258}" width="70" height="22" fill="#c8312b"/><rect x="{x + w + 92}" y="{y - 236}" width="70" height="22" fill="#fff"/>')
    return s


def person(x, y, shirt="#1f6f8b", s=1.0, skin="#c68a5b", hat=False):
    out = (f'<rect x="{x - 16 * s:.0f}" y="{y - 70 * s:.0f}" width="{32 * s:.0f}" height="{52 * s:.0f}" rx="{12 * s:.0f}" fill="{shirt}"/>'
           f'<rect x="{x - 13 * s:.0f}" y="{y - 22 * s:.0f}" width="{11 * s:.0f}" height="{24 * s:.0f}" rx="4" fill="#3b3b3b"/>'
           f'<rect x="{x + 2 * s:.0f}" y="{y - 22 * s:.0f}" width="{11 * s:.0f}" height="{24 * s:.0f}" rx="4" fill="#3b3b3b"/>'
           f'<circle cx="{x}" cy="{y - 86 * s:.0f}" r="{15 * s:.0f}" fill="{skin}"/>')
    if hat:
        out += f'<path d="M{x - 34 * s:.0f} {y - 92 * s:.0f} L{x} {y - 122 * s:.0f} L{x + 34 * s:.0f} {y - 92 * s:.0f} Z" fill="#d9b25c"/>'
    return out


def stall(x, y, w=360, awning=("#c46a16", "#fbe7c6")):
    stripes = "".join(f'<rect x="{x + i * w / 8:.0f}" y="{y - 210}" width="{w / 8:.0f}" height="46" fill="{awning[i % 2]}"/>' for i in range(8))
    scallops = "".join(f'<circle cx="{x + w / 16 + i * w / 8:.0f}" cy="{y - 164}" r="{w / 16:.0f}" fill="{awning[i % 2]}"/>' for i in range(8))
    return (f'<rect x="{x + 10}" y="{y - 164}" width="10" height="164" fill="#7a4e2c"/><rect x="{x + w - 20}" y="{y - 164}" width="10" height="164" fill="#7a4e2c"/>'
            f'{stripes}{scallops}<rect x="{x}" y="{y - 80}" width="{w}" height="80" rx="6" fill="#a8743f"/>'
            f'<rect x="{x}" y="{y - 86}" width="{w}" height="10" fill="#8a5a2c"/>')


def jars(x, y, n=6, colors=("#e0a526", "#b5452f", "#5f9e57")):
    s = ""
    for i in range(n):
        c = colors[i % len(colors)]
        s += (f'<rect x="{x + i * 52}" y="{y - 58}" width="40" height="52" rx="8" fill="{c}"/>'
              f'<rect x="{x + i * 52 + 4}" y="{y - 68}" width="32" height="12" rx="3" fill="#6b4a2b"/>'
              f'<rect x="{x + i * 52 + 8}" y="{y - 42}" width="24" height="16" rx="2" fill="#fff" opacity=".85"/>')
    return s


def baskets(x, y, n=4):
    s = ""
    for i in range(n):
        cx = x + i * 110
        s += f'<path d="M{cx} {y - 70} h90 l-12 70 h-66 Z" fill="#c9995a"/>'
        for k in range(5):
            s += f'<line x1="{cx + 4 + k * 4}" y1="{y - 60 + k * 13}" x2="{cx + 86 - k * 4}" y2="{y - 60 + k * 13}" stroke="#8a6232" stroke-width="3"/>'
        s += f'<path d="M{cx + 10} {y - 70} q35 -50 70 0" stroke="#8a6232" stroke-width="6" fill="none"/>'
    return s


def bowls(x, y, n=4):
    s = ""
    for i in range(n):
        cx = x + i * 90
        s += (f'<path d="M{cx} {y - 40} h70 a35 30 0 0 1 -70 0 Z" fill="#f4e6cf"/>'
              f'<ellipse cx="{cx + 35}" cy="{y - 40}" rx="35" ry="8" fill="#e0a526"/>'
              f'<path d="M{cx + 22} {y - 60} q-8 -14 0 -28 M{cx + 40} {y - 62} q-8 -14 0 -28" stroke="#fff" stroke-width="4" fill="none" opacity=".8"/>')
    return s


def veg_rows(top, p):
    s = f'<rect x="0" y="{top}" width="{W}" height="{H - top}" fill="#8b6b45"/>'
    for i in range(8):
        y = top + 20 + i * (H - top) / 8
        s += f'<rect x="0" y="{y:.0f}" width="{W}" height="{(H - top) / 16:.0f}" fill="#6f5235"/>'
        for x in range(30, W, 70 + i * 6):
            r = 12 + i * 3
            s += f'<circle cx="{x}" cy="{y:.0f}" r="{r}" fill="{p["mid"] if (x // 70) % 2 else p["near"]}"/>'
    return s


def water(top, color="#7fb7c9"):
    s = f'<rect x="0" y="{top}" width="{W}" height="{H - top}" fill="{color}"/>'
    for i in range(10):
        y = top + 15 + i * 14
        s += f'<path d="M{(i * 97) % 300} {y} q40 -8 80 0 t80 0" stroke="#e8f4f7" stroke-width="3" fill="none" opacity=".7"/>'
    return s


def base(p, sun_x=880, sun_y=210, far_y=430, mid_y=500):
    return (f'<defs><linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{p["sky1"]}"/>'
            f'<stop offset="1" stop-color="{p["sky2"]}"/></linearGradient></defs>'
            f'<rect width="{W}" height="{H}" fill="url(#sky)"/><circle cx="{sun_x}" cy="{sun_y}" r="70" fill="{p["sun"]}" opacity=".95"/>'
            f'<g fill="#fff" opacity=".75"><ellipse cx="260" cy="150" rx="90" ry="24"/><ellipse cx="320" cy="135" rx="60" ry="22"/>'
            f'<ellipse cx="640" cy="110" rx="70" ry="18"/></g>'
            + hills(far_y, 120, p["far"], 1) + hills(mid_y, 90, p["mid"], 2))


def ground(y, color):
    return f'<rect x="0" y="{y}" width="{W}" height="{H - y}" fill="{color}"/>'


def scenes():
    P = PALETTES
    return {
        "hero-sawah": base(P["pagi"]) + rice_field(560, P["pagi"]) + house(140, 560, 150, 90) + house(330, 555, 120, 75, roof="#8a3b26")
        + tree(560, 565, 0.9) + palm(1010, 600, 1.1) + tree(1120, 575, 0.8, "#3d7a45") + person(760, 700, "#c46a16", 1.1, hat=True),
        "sawah-padi": base(P["siang"]) + rice_field(520, P["siang"]) + palm(150, 560) + person(640, 690, "#1f6f8b", 1.2, hat=True)
        + person(820, 670, "#b5452f", 1.0, hat=True) + tree(1080, 540, 0.8),
        "sawah-senja": base(P["senja"], 600, 380) + rice_field(560, P["senja"]) + palm(220, 600, 1.2) + palm(980, 590, 0.9) + house(420, 560, 130, 80, roof="#5a2a1a"),
        "kebun-sayur": base(P["teduh"]) + veg_rows(520, P["teduh"]) + person(300, 640, "#5f9e57", 1.1, hat=True) + tree(1050, 530, 0.9),
        "balai-desa": base(P["siang"]) + ground(600, "#c9d6a3") + hall(390, 600, 420, 150) + tree(180, 610, 1.0) + tree(1040, 610, 1.0)
        + person(520, 720, "#1f6f8b") + person(600, 724, "#b5452f") + person(680, 720, "#5f9e57"),
        "kantor-desa": base(P["pagi"]) + ground(590, "#d4dcae") + hall(360, 590, 400, 140, roof="#2f6b3a", flag=True) + tree(150, 600, 0.9),
        "umkm-olahan": base(P["pagi"]) + ground(600, "#e3d3b0") + stall(420, 640) + jars(450, 560) + person(330, 720, "#7a4e9a") + person(880, 720, "#1f6f8b"),
        "kerajinan-bambu": base(P["teduh"]) + ground(580, "#d9c9a0") + baskets(330, 700, 5) + person(220, 700, "#b5452f", 1.1)
        + ''.join(f'<rect x="{900 + i * 22}" y="420" width="14" height="280" rx="6" fill="#c9a659"/>' for i in range(6)),
        "kuliner-desa": base(P["senja"], 950, 260) + ground(600, "#e3cfa6") + stall(380, 650, 420, ("#b5452f", "#fbe7c6")) + bowls(420, 570, 4) + person(860, 730, "#2f6b3a"),
        "taman-desa": base(P["siang"]) + ground(560, "#a9cf6d") + tree(200, 600, 1.2) + tree(980, 590, 1.1, "#3d7a45") + tree(620, 560, 0.8)
        + '<path d="M500 800 Q600 640 700 560" stroke="#e8dcc0" stroke-width="70" fill="none"/>'
        + '<rect x="760" y="650" width="140" height="14" rx="4" fill="#8a5a2c"/><rect x="770" y="664" width="10" height="34" fill="#6b4a2b"/><rect x="880" y="664" width="10" height="34" fill="#6b4a2b"/>',
        "posyandu": base(P["pagi"]) + ground(600, "#d4dcae") + house(420, 600, 360, 150, wall="#fdf6ec", roof="#1f6f8b")
        + '<rect x="565" y="400" width="70" height="70" rx="14" fill="#fff"/><path d="M600 412 v46 M577 435 h46" stroke="#b5452f" stroke-width="12" stroke-linecap="round"/>'
        + person(330, 720, "#e0a526") + person(870, 720, "#b5452f", 0.8),
        "sekolah": base(P["siang"]) + ground(600, "#d4dcae") + hall(330, 600, 480, 130, roof="#b5452f", flag=True) + person(400, 730, "#ffffff", 0.8) + person(460, 730, "#ffffff", 0.8)
        + person(520, 730, "#ffffff", 0.8) + tree(140, 610, 0.9),
        "gotong-royong": base(P["pagi"]) + ground(580, "#c9d6a3") + '<path d="M0 700 L1200 650 L1200 720 L0 780 Z" fill="#cbb994"/>'
        + person(260, 700, "#2f6b3a", 1.1, hat=True) + person(420, 690, "#c46a16", 1.1) + person(580, 680, "#1f6f8b", 1.1, hat=True)
        + person(740, 670, "#b5452f", 1.1) + person(900, 662, "#7a4e9a", 1.1, hat=True) + tree(1100, 600, 0.8),
        "lapangan": base(P["siang"]) + ground(520, "#7fbf5f") + '<rect x="160" y="560" width="880" height="220" fill="none" stroke="#fff" stroke-width="6"/>'
        + '<line x1="600" y1="560" x2="600" y2="780" stroke="#fff" stroke-width="6"/><circle cx="600" cy="670" r="50" fill="none" stroke="#fff" stroke-width="6"/>'
        + person(420, 700, "#c46a16", 0.9) + person(760, 690, "#1f6f8b", 0.9),
        "musyawarah": base(P["teduh"]) + ground(580, "#e3d3b0") + '<rect x="300" y="610" width="600" height="40" rx="10" fill="#8a5a2c"/>'
        + person(360, 720, "#2f6b3a") + person(480, 720, "#c46a16") + person(600, 720, "#1f6f8b") + person(720, 720, "#7a4e9a") + person(840, 720, "#b5452f")
        + '<rect x="420" y="330" width="360" height="200" rx="12" fill="#fdf6ec" stroke="#2f6b3a" stroke-width="6"/>'
        + ''.join(f'<rect x="{470 + i * 70}" y="{470 - h}" width="40" height="{h}" fill="#5f9e57"/>' for i, h in enumerate([60, 100, 80, 120])),
        "pelatihan": base(P["pagi"]) + ground(580, "#e8dcc5") + ''.join(
            f'<rect x="{220 + i * 260}" y="620" width="200" height="16" rx="4" fill="#8a5a2c"/>'
            f'<path d="M{250 + i * 260} 620 v-50 h100 v20 h-20 v30" stroke="#4a4a4a" stroke-width="12" fill="none"/>'
            + person(320 + i * 260, 740, ["#b5452f", "#1f6f8b", "#7a4e9a"][i], 0.95) for i in range(3)),
        "irigasi": base(P["siang"]) + rice_field(540, P["siang"]) + '<path d="M0 760 C300 700 700 700 1200 640 L1200 700 C700 760 300 780 0 800 Z" fill="#7fb7c9"/>' + palm(1000, 600),
        "jalan-desa": base(P["pagi"]) + ground(560, "#a9cf6d") + '<path d="M480 800 L580 560 L640 560 L800 800 Z" fill="#9b9b8f"/>'
        + '<path d="M630 800 L607 560" stroke="#fff" stroke-width="6" stroke-dasharray="30 30"/>' + house(150, 620, 140, 85) + house(900, 610, 150, 90, roof="#2f6b3a") + tree(380, 600, 0.7),
        "bank-sampah": base(P["teduh"]) + ground(590, "#c9d6a3") + ''.join(
            f'<rect x="{300 + i * 170}" y="560" width="120" height="140" rx="12" fill="{c}"/><rect x="{290 + i * 170}" y="545" width="140" height="22" rx="6" fill="{c}" opacity=".8"/>'
            for i, c in enumerate(["#2f6b3a", "#e0a526", "#1f6f8b", "#b5452f"])) + person(1020, 720, "#5f9e57"),
        "pasar-desa": base(P["siang"]) + ground(600, "#e3d3b0") + stall(120, 650, 320, ("#2f6b3a", "#f4e6cf")) + stall(760, 650, 320, ("#c46a16", "#fbe7c6"))
        + jars(150, 570, 5) + bowls(790, 570, 3) + person(600, 740, "#1f6f8b") + person(520, 740, "#b5452f", 0.9),
    }


def main():
    os.makedirs(OUT, exist_ok=True)
    for name, body in scenes().items():
        svg = (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" '
               f'aria-label="Ilustrasi {name.replace("-", " ")}">{body}</svg>')
        with open(os.path.join(OUT, f"{name}.svg"), "w", encoding="utf-8") as f:
            f.write(svg)
    print(f"{len(scenes())} ilustrasi dibuat di {os.path.abspath(OUT)}")


if __name__ == "__main__":
    main()
