<footer>
    <div class="container footer-container">
        <div class="footer-content">
            <div>
                <div class="footer-brand">
                    <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="Logo">
                    <div class="footer-brand-text">
                        <div class="footer-brand-name">KOPI LOKA</div>
                        <div class="footer-brand-tagline">Temani harimu dengan secangkir kopi</div>
                    </div>
                </div>
                <p class="footer-text">
                    &copy; {{ date('Y') }} KOPI LOKA. All rights reserved.
                </p>
            </div>
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/praf.bude?igsh=MXJ0aHdjMmV2b2sxYQ==" class="social-link" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-link" aria-label="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
      :root {
            --primary-color: #ff6600;
            --primary-hover: #e65c00;
            --primary-light: #fff0e6;
            --text-dark: #333333;
            --text-light: #666666;
            --text-muted: #999999;
            --bg-light: #f9f9f9;
            --border-color: #eeeeee;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
 /* Footer */
 footer {
            background-color: white;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        .footer-container {
            padding: 2rem 1.5rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            display: flex;
            align-items: center;
        }

        .footer-brand img {
            height: 40px;
            margin-right: 1rem;
        }

        .footer-brand-text {
            display: flex;
            flex-direction: column;

        }

        .footer-brand-name {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .footer-brand-tagline {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 800;
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 1rem;
            font-weight: 800;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: var(--primary-color);
            background-color: var(--primary-light);
            border-radius: 50%;
            font-size: 1.125rem;
            transition: var(--transition);
        }

        .social-link:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
</style>