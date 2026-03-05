@extends('layouts.app')
@section('title', 'POS Terminal')

@push('styles')
    <style>
        .content-area {
            padding: 0 !important;
            overflow: hidden !important;
        }

        :root {
            --pos-panel: #13131F;
            --pos-card: rgba(255, 255, 255, 0.03);
            --pos-border: rgba(255, 255, 255, 0.07);
            --blue: #003087;
            --blue-mid: #1a4db3;
            --red: #CC0001;
            --muted: #6B7280;
            --muted-lt: #9CA3AF;
            --text: #E8E4DC;
        }

        .pos-shell {
            display: flex;
            height: 100%;
            overflow: hidden;
        }

        /* ── LEFT ───────────────────────────────────── */
        .pos-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 16px 20px 20px;
            gap: 12px;
            min-width: 0;
            overflow: hidden;
            border-right: 1px solid var(--pos-border);
        }

        /* Search + toggle row */
        .search-toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .search-wrap {
            position: relative;
            flex: 1;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: var(--muted);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--pos-border);
            border-radius: 11px;
            padding: 11px 16px 11px 42px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            outline: none;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: rgba(0, 80, 200, 0.5);
            background: rgba(0, 48, 135, 0.08);
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.12);
        }

        .search-input::placeholder {
            color: rgba(156, 163, 175, 0.4);
        }

        /* View toggle */
        .view-toggle {
            display: flex;
            gap: 3px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--pos-border);
            border-radius: 10px;
            padding: 3px;
            flex-shrink: 0;
        }

        .view-btn {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            border: none;
            background: none;
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .view-btn.active {
            background: rgba(0, 48, 135, 0.4);
            color: #93C5FD;
        }

        .view-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted-lt);
        }

        .view-btn svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        /* ── GRID VIEW ───────────────────────────────── */
        #product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            overflow-y: auto;
            flex: 1;
            padding-right: 4px;
            align-content: start;
        }

        #product-grid::-webkit-scrollbar {
            width: 5px;
        }

        #product-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        #product-grid::-webkit-scrollbar-thumb {
            background: var(--pos-border);
            border-radius: 3px;
        }

        .product-card {
            background: var(--pos-card);
            border: 1px solid var(--pos-border);
            border-radius: 13px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.18s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            border-color: rgba(0, 80, 200, 0.4);
            background: rgba(0, 48, 135, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .product-card:active {
            transform: translateY(0);
        }

        .product-img-wrap {
            width: 100%;
            aspect-ratio: 1;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.04);
            position: relative;
            flex-shrink: 0;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img-wrap img {
            transform: scale(1.05);
        }

        .out-of-stock-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #FCA5A5;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .product-info {
            padding: 9px 11px 11px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .product-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #4ADE80;
        }

        .product-stock {
            font-size: 10.5px;
            color: var(--muted);
        }

        .btn-add {
            margin: 0 10px 10px;
            padding: 7px;
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(0, 48, 135, 0.3);
        }

        .btn-add:hover {
            box-shadow: 0 4px 14px rgba(0, 48, 135, 0.45);
        }

        .btn-add:disabled {
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ── LIST VIEW ───────────────────────────────── */
        #product-grid.list-view {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        #product-grid.list-view .product-card {
            flex-direction: row;
            align-items: stretch;
            border-radius: 10px;
            height: 66px;
        }

        #product-grid.list-view .product-img-wrap {
            width: 66px;
            height: 66px;
            aspect-ratio: unset;
            border-radius: 0;
            flex-shrink: 0;
        }

        #product-grid.list-view .product-card:hover .product-img-wrap img {
            transform: scale(1.1);
        }

        #product-grid.list-view .product-info {
            flex-direction: row;
            align-items: center;
            padding: 0 14px;
            gap: 0;
            flex: 1;
            min-width: 0;
        }

        #product-grid.list-view .product-name {
            flex: 1;
            -webkit-line-clamp: 1;
            font-size: 13px;
            min-width: 0;
        }

        #product-grid.list-view .product-price {
            font-size: 14px;
            min-width: 60px;
            text-align: right;
            padding: 0 10px;
        }

        #product-grid.list-view .product-stock {
            font-size: 11px;
            min-width: 68px;
            text-align: right;
            padding-right: 10px;
            white-space: nowrap;
        }

        #product-grid.list-view .btn-add {
            margin: 0;
            border-radius: 0 10px 10px 0;
            padding: 0 16px;
            height: 100%;
            font-size: 12px;
            white-space: nowrap;
            width: auto;
            align-self: stretch;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── RIGHT: CART ─────────────────────────────── */
        .pos-right {
            width: 320px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            background: var(--pos-panel);
            overflow: hidden;
        }

        .cart-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--pos-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .cart-title {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-count {
            background: rgba(0, 48, 135, 0.3);
            border: 1px solid rgba(0, 48, 135, 0.45);
            color: #93C5FD;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-clear-cart {
            background: none;
            border: 1px solid rgba(204, 0, 1, 0.25);
            color: rgba(248, 113, 113, 0.6);
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.15s;
        }

        .btn-clear-cart:hover {
            border-color: rgba(204, 0, 1, 0.5);
            color: #FCA5A5;
            background: rgba(204, 0, 1, 0.08);
        }

        #cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px 16px;
        }

        #cart-items::-webkit-scrollbar {
            width: 4px;
        }

        #cart-items::-webkit-scrollbar-track {
            background: transparent;
        }

        #cart-items::-webkit-scrollbar-thumb {
            background: var(--pos-border);
            border-radius: 2px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            animation: slideIn 0.18s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(8px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-price {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--muted-lt);
            margin-top: 1px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--pos-border);
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-btn {
            width: 26px;
            height: 26px;
            background: none;
            border: none;
            color: var(--muted-lt);
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.12s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .qty-val {
            width: 28px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            font-family: 'IBM Plex Mono', monospace;
            border-left: 1px solid var(--pos-border);
            border-right: 1px solid var(--pos-border);
            line-height: 26px;
        }

        .cart-item-total {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            min-width: 52px;
            text-align: right;
        }

        .btn-remove {
            background: none;
            border: none;
            color: rgba(248, 113, 113, 0.4);
            cursor: pointer;
            font-size: 16px;
            padding: 2px 4px;
            transition: color 0.12s;
            line-height: 1;
        }

        .btn-remove:hover {
            color: #FCA5A5;
        }

        .cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 10px;
            color: var(--muted);
        }

        .cart-empty-icon {
            font-size: 40px;
            opacity: 0.3;
        }

        .cart-empty-text {
            font-size: 13px;
        }

        .cart-footer {
            border-top: 1px solid var(--pos-border);
            padding: 16px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 13px;
            color: var(--muted-lt);
            font-weight: 500;
        }

        .total-amount {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 24px;
            font-weight: 700;
            color: #fff;
        }

        .payment-tabs {
            display: flex;
            gap: 6px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--pos-border);
            border-radius: 10px;
            padding: 4px;
        }

        .payment-tab {
            flex: 1;
            padding: 8px 6px;
            border-radius: 7px;
            border: none;
            background: none;
            color: var(--muted-lt);
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.18s;
            text-align: center;
        }

        .payment-tab.active {
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            color: #fff;
            box-shadow: 0 2px 8px rgba(0, 48, 135, 0.35);
        }

        .cash-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--muted);
        }

        .cash-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--pos-border);
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 15px;
            font-family: 'IBM Plex Mono', monospace;
            color: #fff;
            outline: none;
            transition: all 0.2s;
            width: 100%;
        }

        .cash-input:focus {
            border-color: rgba(22, 163, 74, 0.5);
            background: rgba(22, 163, 74, 0.05);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        }

        .change-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(22, 163, 74, 0.08);
            border: 1px solid rgba(22, 163, 74, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .change-label {
            font-size: 12px;
            color: #86EFAC;
            font-weight: 600;
        }

        .change-val {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 15px;
            font-weight: 700;
            color: #86EFAC;
        }

        .btn-checkout {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #15803D, #16A34A);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.22s;
            box-shadow: 0 4px 16px rgba(22, 163, 74, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-checkout:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.45);
        }

        .btn-checkout:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-checkout:disabled {
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ── KHQR MODAL ──────────────────────────────── */
        .qr-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 20px;
        }

        .qr-backdrop.open {
            display: flex;
        }

        .qr-modal {
            background: #13131F;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            width: 100%;
            max-width: 600px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.6);
            animation: modalIn 0.3s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .qr-modal-header {
            background: linear-gradient(135deg, #001B5C 0%, #003087 60%, #1a4db3 100%);
            padding: 22px 28px;
            text-align: center;
            position: relative;
        }

        .qr-modal-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        }

        .modal-flag {
            display: flex;
            height: 3px;
            width: 50px;
            border-radius: 2px;
            overflow: hidden;
            gap: 1px;
            margin: 0 auto 14px;
        }

        .modal-flag span:nth-child(1),
        .modal-flag span:nth-child(3) {
            background: var(--red);
            flex: 1;
        }

        .modal-flag span:nth-child(2) {
            background: #4a90d9;
            flex: 2;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 4px;
        }

        .modal-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
        }

        .qr-modal-body {
            padding: 24px;
        }

        .qr-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        .qr-card {
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            border: 1px solid;
        }

        .qr-card.usd {
            background: rgba(0, 48, 135, 0.1);
            border-color: rgba(0, 48, 135, 0.3);
        }

        .qr-card.khr {
            background: rgba(204, 0, 1, 0.08);
            border-color: rgba(204, 0, 1, 0.25);
        }

        .qr-currency-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .qr-card.usd .qr-currency-label {
            color: #93C5FD;
        }

        .qr-card.khr .qr-currency-label {
            color: #FCA5A5;
        }

        .qr-amount {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .qr-card.usd .qr-amount {
            color: #60A5FA;
        }

        .qr-card.khr .qr-amount {
            color: #F87171;
        }

        .qr-image-wrap {
            background: #fff;
            border-radius: 10px;
            padding: 10px;
            display: inline-block;
            width: 100%;
        }

        .qr-image-wrap img {
            width: 100%;
            display: block;
            border-radius: 4px;
        }

        .qr-banks {
            font-size: 10px;
            color: var(--muted);
            margin-top: 8px;
        }

        .status-box {
            margin-bottom: 16px;
        }

        .status-waiting {
            text-align: center;
            padding: 12px;
            background: rgba(0, 48, 135, 0.1);
            border: 1px solid rgba(0, 48, 135, 0.25);
            border-radius: 12px;
            font-size: 13px;
            color: #93C5FD;
            font-weight: 500;
        }

        .status-waiting .pulse-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            background: #93C5FD;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulseDot 1.4s ease infinite;
        }

        @keyframes pulseDot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.3;
                transform: scale(0.7);
            }
        }

        .status-success {
            display: none;
            padding: 16px;
            background: rgba(22, 163, 74, 0.12);
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 12px;
            text-align: center;
        }

        .status-success .big-check {
            font-size: 32px;
            margin-bottom: 6px;
        }

        .status-success .success-title {
            font-size: 16px;
            font-weight: 700;
            color: #86EFAC;
            margin-bottom: 4px;
        }

        .status-success .success-sub {
            font-size: 12px;
            color: var(--muted-lt);
        }

        .status-expired {
            display: none;
            padding: 12px;
            background: rgba(204, 0, 1, 0.1);
            border: 1px solid rgba(204, 0, 1, 0.25);
            border-radius: 12px;
            text-align: center;
            font-size: 13px;
            color: #FCA5A5;
        }

        .countdown-wrap {
            margin-bottom: 16px;
        }

        .countdown-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--muted-lt);
            margin-bottom: 6px;
        }

        .countdown-timer {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 700;
            color: #F97316;
        }

        .countdown-track {
            height: 5px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 999px;
            overflow: hidden;
        }

        .countdown-bar {
            height: 100%;
            border-radius: 999px;
            transition: width 1s linear, background 1s ease;
            background: #22C55E;
        }

        .exchange-note {
            text-align: center;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 14px;
        }

        .btn-cancel-qr {
            width: 100%;
            padding: 11px;
            background: rgba(204, 0, 1, 0.12);
            border: 1px solid rgba(204, 0, 1, 0.3);
            border-radius: 10px;
            color: #FCA5A5;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.18s;
        }

        .btn-cancel-qr:hover {
            background: rgba(204, 0, 1, 0.22);
        }

        @media (max-width: 900px) {
            #product-grid:not(.list-view) {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }

        @media (max-width: 700px) {
            .pos-shell {
                flex-direction: column;
            }

            .pos-right {
                width: 100%;
                height: 380px;
                border-right: none;
                border-top: 1px solid var(--pos-border);
            }

            .pos-left {
                flex: none;
                height: calc(100% - 380px);
            }

            .qr-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pos-shell">

        {{-- ── LEFT: PRODUCTS ──────────────────────────── --}}
        <div class="pos-left">

            {{-- Search + View Toggle --}}
            <div class="search-toggle-row">
                <div class="search-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="search" class="search-input" placeholder="Scan barcode or search product..."
                        autofocus>
                </div>
                <div class="view-toggle">
                    {{-- Grid icon --}}
                    <button class="view-btn active" id="btn-grid" onclick="setView('grid')" title="Grid view">
                        <svg viewBox="0 0 16 16">
                            <rect x="1" y="1" width="6" height="6" rx="1" />
                            <rect x="9" y="1" width="6" height="6" rx="1" />
                            <rect x="1" y="9" width="6" height="6" rx="1" />
                            <rect x="9" y="9" width="6" height="6" rx="1" />
                        </svg>
                    </button>
                    {{-- List icon --}}
                    <button class="view-btn" id="btn-list" onclick="setView('list')" title="List view">
                        <svg viewBox="0 0 16 16">
                            <rect x="1" y="2" width="14" height="2.5" rx="1" />
                            <rect x="1" y="6.75" width="14" height="2.5" rx="1" />
                            <rect x="1" y="11.5" width="14" height="2.5" rx="1" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Product grid --}}
            <div id="product-grid">
                @foreach ($products as $product)
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}" loading="lazy">
                            @if ($product->stock <= 0)
                                <div class="out-of-stock-overlay">Out of Stock</div>
                            @endif
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-price">${{ number_format($product->sell_price, 2) }}</div>
                            <div class="product-stock">Stock: {{ $product->stock }}</div>
                        </div>
                        <button class="btn-add" onclick="addToCart({{ $product->id }})"
                            {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            + Add to Cart
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── RIGHT: CART ──────────────────────────────── --}}
        <div class="pos-right">
            <div class="cart-header">
                <div class="cart-title">
                    🛒 Cart <span class="cart-count" id="cartCount">0</span>
                </div>
                <button class="btn-clear-cart" onclick="clearCart()">Clear</button>
            </div>
            <div id="cart-items">
                <div class="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <div class="cart-empty-text">Cart is empty</div>
                </div>
            </div>
            <div class="cart-footer">
                <div class="total-row">
                    <span class="total-label">Total</span>
                    <span class="total-amount" id="totalAmount">$0.00</span>
                </div>
                <div class="payment-tabs">
                    <button class="payment-tab active" id="tab-cash" onclick="setPayment('cash')">💵 Cash</button>
                    <button class="payment-tab" id="tab-khqr" onclick="setPayment('khqr')">📱 KHQR</button>
                    <button class="payment-tab" id="tab-aba" onclick="setPayment('aba')">🏦 ABA</button>
                </div>
                <div class="cash-section" id="cashSection">
                    <div class="input-label">Cash Received</div>
                    <input type="number" id="cashInput" class="cash-input" placeholder="0.00" step="0.01">
                    <div class="change-row">
                        <span class="change-label">Change</span>
                        <span class="change-val" id="changeAmount">$0.00</span>
                    </div>
                </div>
                <button id="checkoutBtn" class="btn-checkout" onclick="checkout()" disabled>✓ Checkout</button>
            </div>
        </div>
    </div>

    {{-- ── KHQR MODAL ── --}}
    <div class="qr-backdrop" id="qrBackdrop">
        <div class="qr-modal">
            <div class="qr-modal-header">
                <div class="modal-flag"><span></span><span></span><span></span></div>
                <div class="modal-title">Scan to Pay with KHQR</div>
                <div class="modal-subtitle">Choose either currency — all NBC Bakong banks supported</div>
            </div>
            <div class="qr-modal-body">
                <div class="qr-grid">
                    <div class="qr-card usd">
                        <div class="qr-currency-label">🇺🇸 US Dollar</div>
                        <div class="qr-amount" id="usdAmount"></div>
                        <div class="qr-image-wrap"><img id="qrImageUSD" src="" alt="USD QR"></div>
                        <div class="qr-banks">ABA · Wing · ACLEDA · all banks</div>
                    </div>
                    <div class="qr-card khr">
                        <div class="qr-currency-label">🇰🇭 Khmer Riel</div>
                        <div class="qr-amount" id="khrAmount"></div>
                        <div class="qr-image-wrap"><img id="qrImageKHR" src="" alt="KHR QR"></div>
                        <div class="qr-banks">ABA · Wing · ACLEDA · all banks</div>
                    </div>
                </div>
                <div class="status-box">
                    <div class="status-waiting" id="statusWaiting"><span class="pulse-dot"></span>Waiting for payment on
                        either QR...</div>
                    <div class="status-success" id="statusSuccess">
                        <div class="big-check">✅</div>
                        <div class="success-title">Payment Received!</div>
                        <div class="success-sub" id="successCurrency"></div>
                        <div class="success-sub" style="margin-top:4px;color:#6B7280;">Redirecting to receipt...</div>
                    </div>
                    <div class="status-expired" id="statusExpired">❌ QR Codes Expired — click cancel and try again.</div>
                </div>
                <div class="countdown-wrap" id="countdownArea">
                    <div class="countdown-meta">
                        <span>Expires in</span>
                        <span class="countdown-timer" id="countdownTimer">5:00</span>
                    </div>
                    <div class="countdown-track">
                        <div class="countdown-bar" id="countdownBar" style="width:100%"></div>
                    </div>
                </div>
                <div class="exchange-note" id="exchangeNote"></div>
                <button class="btn-cancel-qr" id="cancelQrBtn" onclick="closeQrPopup()">Cancel Transaction</button>
            </div>
        </div>
    </div>

    {{-- ABA PayWay Modal --}}
    </div>  {{-- closes #qrBackdrop --}}

    {{-- ── ABA PAYWAY MODAL — OUTSIDE #qrBackdrop ── --}}
    <div id="abaBackdrop"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85);
                backdrop-filter:blur(8px); z-index:300; align-items:center;
                justify-content:center; padding:20px;">
        <div style="background:#1C1C2E; border:1px solid rgba(255,255,255,0.1);
                    border-radius:24px; width:100%; max-width:360px; padding:32px 28px;
                    text-align:center; box-shadow:0 24px 80px rgba(0,0,0,0.6);">

            <div style="width:56px;height:56px;background:linear-gradient(135deg,#003087,#1a4db3);
                        border-radius:16px;display:flex;align-items:center;justify-content:center;
                        font-size:26px;margin:0 auto 16px;box-shadow:0 8px 24px rgba(0,48,135,0.4);">🏦</div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:#fff;margin-bottom:4px;">ABA PayWay</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.45);margin-bottom:20px;">Scan with ABA Mobile app to pay</div>

            <div id="abaAmount" style="font-family:'IBM Plex Mono',monospace;font-size:22px;font-weight:700;color:#60A5FA;margin-bottom:16px;"></div>

            <div style="background:#fff;border-radius:16px;padding:12px;margin:0 auto 20px;width:220px;height:220px;display:flex;align-items:center;justify-content:center;">
                <img id="abaQrImage" src="" alt="ABA QR Code" style="width:100%;height:100%;object-fit:contain;border-radius:8px;">
            </div>

            <div id="abaWaiting" style="display:flex;align-items:center;justify-content:center;gap:8px;
                        padding:10px 16px;background:rgba(0,48,135,0.15);border:1px solid rgba(0,48,135,0.3);
                        border-radius:10px;font-size:13px;color:#93C5FD;font-weight:500;margin-bottom:16px;">
                <span style="width:7px;height:7px;background:#93C5FD;border-radius:50%;animation:pulseDot 1.4s ease infinite;display:inline-block;"></span>
                Waiting for payment...
            </div>

            <div id="abaSuccess" style="display:none;padding:16px;background:rgba(22,163,74,0.12);
                        border:1px solid rgba(22,163,74,0.3);border-radius:12px;margin-bottom:16px;">
                <div style="font-size:28px;margin-bottom:6px;">✅</div>
                <div style="font-size:15px;font-weight:700;color:#86EFAC;">Payment Confirmed!</div>
                <div style="font-size:12px;color:#6B7280;margin-top:4px;">Redirecting to receipt...</div>
            </div>

            <button onclick="closeAbaModal()"
                    style="width:100%;padding:11px;background:rgba(204,0,1,0.12);border:1px solid rgba(204,0,1,0.3);
                           border-radius:10px;color:#FCA5A5;font-size:13px;font-weight:600;
                           font-family:'DM Sans',sans-serif;cursor:pointer;">
                Cancel Transaction
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const POS_STATE = {
            paymentInterval:   null,
            countdownInterval: null,
            abaInterval:       null,   // ← add this
            totalAmount:       0,
            paymentMethod:     'cash',
            viewMode:          localStorage.getItem('pos_view') || 'grid',
        };

        // ── View toggle (persists via localStorage) ───────
        function setView(mode) {
            POS_STATE.viewMode = mode;
            localStorage.setItem('pos_view', mode);
            const grid = document.getElementById('product-grid');
            grid.classList.toggle('list-view', mode === 'list');
            document.getElementById('btn-grid').classList.toggle('active', mode === 'grid');
            document.getElementById('btn-list').classList.toggle('active', mode === 'list');
        }

        // ── Payment tabs ──────────────────────────────────
        function setPayment(method) {
            POS_STATE.paymentMethod = method;
            ['cash','khqr','aba'].forEach(m => {
                document.getElementById(`tab-${m}`).classList.toggle('active', m === method);
            });
            document.getElementById('cashSection').style.display = method === 'cash' ? 'block' : 'none';
        }

        // ── Init ──────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            setView(POS_STATE.viewMode); // restore saved preference
            document.getElementById('cashInput').addEventListener('input', calculateChange);

            let searchTimeout = null;
            document.getElementById('search').addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetch(
                            `{{ route('admin.pos.search') }}?search=${encodeURIComponent(this.value)}`)
                        .then(r => r.json())
                        .then(renderProducts);
                }, 280);
            });

            loadCart();
        });

        // ── Render products ───────────────────────────────
        function renderProducts(products) {
            const grid = document.getElementById('product-grid');
            if (!products.length) {
                grid.innerHTML =
                    `<div style="grid-column:1/-1;text-align:center;padding:40px;color:#6B7280;font-size:13px;">No products found.</div>`;
                return;
            }
            grid.innerHTML = products.map(p => {
                const img = p.image ? `/storage/${p.image}` : '/images/no-image.png';
                const oos = p.stock <= 0;
                return `<div class="product-card">
            <div class="product-img-wrap">
                <img src="${img}" alt="${p.name}" loading="lazy">
                ${oos ? '<div class="out-of-stock-overlay">Out of Stock</div>' : ''}
            </div>
            <div class="product-info">
                <div class="product-name">${p.name}</div>
                <div class="product-price">$${parseFloat(p.sell_price).toFixed(2)}</div>
                <div class="product-stock">Stock: ${p.stock}</div>
            </div>
            <button class="btn-add" onclick="addToCart(${p.id})" ${oos ? 'disabled' : ''}>+ Add to Cart</button>
        </div>`;
            }).join('');
        }

        // ── Cart ──────────────────────────────────────────
        function addToCart(id) {
            fetch("{{ route('admin.pos.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: id
                })
            }).then(r => r.json()).then(d => {
                if (d.error) {
                    showToast(d.error, 'error');
                    return;
                }
                loadCart();
            });
        }

        function updateQty(id, qty) {
            if (qty <= 0) return removeItem(id);
            fetch("{{ route('admin.pos.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: id,
                    quantity: qty
                })
            }).then(() => loadCart());
        }

        function removeItem(id) {
            fetch("{{ route('admin.pos.remove') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: id
                })
            }).then(() => loadCart());
        }

        function clearCart() {
            if (!confirm('Clear all items from cart?')) return;
            fetch("{{ url('/admin/pos-cart-data') }}").then(r => r.json()).then(data => {
                const ids = Object.keys(data);
                if (!ids.length) return;
                const next = (i) => {
                    if (i >= ids.length) {
                        loadCart();
                        return;
                    }
                    fetch("{{ route('admin.pos.remove') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: ids[i]
                        })
                    }).then(() => next(i + 1));
                };
                next(0);
            });
        }

        function loadCart() {
            fetch("{{ url('/admin/pos-cart-data') }}").then(r => r.json()).then(data => {
                const cartDiv = document.getElementById('cart-items');
                const totalEl = document.getElementById('totalAmount');
                const countEl = document.getElementById('cartCount');
                const checkBtn = document.getElementById('checkoutBtn');
                const keys = Object.keys(data);
                let subtotal = 0,
                    totalQty = 0;
                if (!keys.length) {
                    cartDiv.innerHTML =
                        `<div class="cart-empty"><div class="cart-empty-icon">🛒</div><div class="cart-empty-text">Cart is empty</div></div>`;
                    countEl.textContent = '0';
                    totalEl.textContent = '$0.00';
                    POS_STATE.totalAmount = 0;
                    checkBtn.disabled = true;
                    calculateChange();
                    return;
                }
                cartDiv.innerHTML = keys.map(id => {
                    const item = data[id];
                    const lt = item.price * item.quantity;
                    subtotal += lt;
                    totalQty += item.quantity;
                    return `<div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">$${parseFloat(item.price).toFixed(2)} each</div>
                </div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="updateQty(${id}, ${item.quantity - 1})">−</button>
                    <span class="qty-val">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQty(${id}, ${item.quantity + 1})">+</button>
                </div>
                <span class="cart-item-total">$${lt.toFixed(2)}</span>
                <button class="btn-remove" onclick="removeItem(${id})">×</button>
            </div>`;
                }).join('');
                POS_STATE.totalAmount = subtotal;
                totalEl.textContent = '$' + subtotal.toFixed(2);
                countEl.textContent = totalQty;
                checkBtn.disabled = false;
                calculateChange();
            });
        }

        function calculateChange() {
            const cash = parseFloat(document.getElementById('cashInput').value) || 0;
            document.getElementById('changeAmount').textContent = '$' + Math.max(0, cash - POS_STATE.totalAmount).toFixed(
            2);
        }

        // ── Checkout ──
        // function checkout() {
        //     const method = POS_STATE.paymentMethod; // 'cash', 'khqr', or 'aba'
        //     if (method === 'cash') {
        //         const cash = parseFloat(document.getElementById('cashInput').value) || 0;
        //         if (cash < POS_STATE.totalAmount) {
        //             showToast('Insufficient cash amount!', 'error');
        //             return;
        //         }
        //         fetch("{{ route('admin.pos.checkout') }}", {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //             },
        //             body: JSON.stringify({
        //                 paid_amount: cash
        //             })
        //         }).then(r => r.json()).then(d => {
        //             if (d.error) {
        //                 showToast(d.error, 'error');
        //                 return;
        //             }
        //             window.open(`/admin/pos/receipt/${d.sale_id}`, '_blank');
        //             location.reload();
        //         });
        //         return;
        //     }
        //     if (POS_STATE.totalAmount <= 0) {
        //         showToast('Cart is empty!', 'error');
        //         return;
        //     }
        //     const btn = document.getElementById('checkoutBtn');
        //     btn.disabled = true;
        //     btn.innerHTML = '⏳ Generating QR...';
        //     fetch("{{ route('admin.pos.generateKhqr') }}", {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //             },
        //             body: JSON.stringify({
        //                 amount: POS_STATE.totalAmount
        //             })
        //         })
        //         .then(async res => {
        //             const d = await res.json();
        //             if (!res.ok || d.error) throw new Error(d.error || 'QR generation failed');
        //             return d;
        //         })
        //         .then(d => {
        //             showQrModal(d);
        //             startCountdown(d.expires_at);
        //             pollBothPayments(d.usd.md5, d.khr.md5, d.expires_at);
        //         })
        //         .catch(err => showToast('Could not generate QR: ' + err.message, 'error'))
        //         .finally(() => {
        //             btn.disabled = false;
        //             btn.innerHTML = '✓ Checkout';
        //         });
            
        //     if (method === 'aba') {
        //         if (POS_STATE.totalAmount <= 0) { showToast('Cart is empty!', 'error'); return; }

        //         const btn = document.getElementById('checkoutBtn');
        //         btn.disabled = true;
        //         btn.innerHTML = '⏳ Generating ABA QR...';

        //         fetch("{{ route('admin.pos.payway.generate') }}", {
        //             method: 'POST',
        //             headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        //             body: JSON.stringify({ amount: POS_STATE.totalAmount })
        //         })
        //         .then(r => r.json())
        //         .then(data => {
        //             if (data.error) { showToast(data.error, 'error'); return; }
        //             showAbaModal(data);
        //             pollAbaPayment();
        //         })
        //         .catch(err => showToast('ABA PayWay error: ' + err.message, 'error'))
        //         .finally(() => { btn.disabled = false; btn.innerHTML = '✓ Checkout'; });
        //         return;
        //     }
        // }

        function checkout() {
            const method = POS_STATE.paymentMethod;

            // ── CASH ─────────────────────────────────────────
            if (method === 'cash') {
                const cash = parseFloat(document.getElementById('cashInput').value) || 0;
                if (cash < POS_STATE.totalAmount) {
                    showToast('Insufficient cash amount!', 'error');
                    return;
                }
                fetch("{{ route('admin.pos.checkout') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ paid_amount: cash })
                }).then(r => r.json()).then(d => {
                    if (d.error) { showToast(d.error, 'error'); return; }
                    window.open(`/admin/pos/receipt/${d.sale_id}`, '_blank');
                    location.reload();
                });
                return; // ← stop here
            }

            // ── Guard: cart must not be empty for QR methods ──
            if (POS_STATE.totalAmount <= 0) {
                showToast('Cart is empty!', 'error');
                return;
            }

            const btn = document.getElementById('checkoutBtn');

            // ── KHQR (Bakong) ─────────────────────────────────
            if (method === 'khqr') {
                btn.disabled = true;
                btn.innerHTML = '⏳ Generating QR...';

                fetch("{{ route('admin.pos.generateKhqr') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ amount: POS_STATE.totalAmount })
                })
                .then(async res => {
                    const d = await res.json();
                    if (!res.ok || d.error) throw new Error(d.error || 'QR generation failed');
                    return d;
                })
                .then(d => {
                    showQrModal(d);
                    startCountdown(d.expires_at);
                    pollBothPayments(d.usd.md5, d.khr.md5, d.expires_at);
                })
                .catch(err => showToast('Could not generate QR: ' + err.message, 'error'))
                .finally(() => { btn.disabled = false; btn.innerHTML = '✓ Checkout'; });
                return; // ← stop here
            }

            // ── ABA PayWay ────────────────────────────────────
            if (method === 'aba') {
                btn.disabled = true;
                btn.innerHTML = '⏳ Generating ABA QR...';

                fetch("{{ route('admin.pos.payway.generate') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ amount: POS_STATE.totalAmount })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) { showToast(data.error, 'error'); return; }
                    showAbaModal(data);
                    pollAbaPayment(data.tran_id);
                })
                .catch(err => showToast('ABA PayWay error: ' + err.message, 'error'))
                .finally(() => { btn.disabled = false; btn.innerHTML = '✓ Checkout'; });
                return; // ← stop here
            }
        }

        // ── Poll & countdown ──────────────────────────────
        function pollBothPayments(md5USD, md5KHR, expiresAt) {
            if (POS_STATE.paymentInterval) clearInterval(POS_STATE.paymentInterval);
            let paid = false;
            POS_STATE.paymentInterval = setInterval(() => {
                if (paid) return;
                if (Math.floor(Date.now() / 1000) >= expiresAt) {
                    clearInterval(POS_STATE.paymentInterval);
                    clearInterval(POS_STATE.countdownInterval);
                    showExpiredState();
                    return;
                }
                pollSingle(md5USD, 'usd', () => {
                    paid = true;
                });
                setTimeout(() => {
                    if (!paid) pollSingle(md5KHR, 'khr', () => {
                        paid = true;
                    });
                }, 500);
            }, 3000);
        }

        function pollSingle(md5, currency, onSuccess) {
            fetch("{{ route('admin.pos.verifyKhqr') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    md5,
                    currency
                })
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    onSuccess();
                    clearInterval(POS_STATE.paymentInterval);
                    clearInterval(POS_STATE.countdownInterval);
                    showSuccessState(d.currency);
                    setTimeout(() => {
                        window.location.href = d.receipt_url || `/admin/pos/receipt/${d.sale_id}`;
                    }, 1500);
                }
            }).catch(() => {});
        }

        function startCountdown(expiresAt) {
            if (POS_STATE.countdownInterval) clearInterval(POS_STATE.countdownInterval);
            const total = expiresAt - Math.floor(Date.now() / 1000);
            const bar = document.getElementById('countdownBar'),
                timer = document.getElementById('countdownTimer');
            POS_STATE.countdownInterval = setInterval(() => {
                const rem = expiresAt - Math.floor(Date.now() / 1000);
                if (rem <= 0) {
                    clearInterval(POS_STATE.countdownInterval);
                    timer.textContent = '0:00';
                    bar.style.width = '0%';
                    bar.style.background = '#ef4444';
                    return;
                }
                timer.textContent = `${Math.floor(rem/60)}:${(rem%60).toString().padStart(2,'0')}`;
                const pct = (rem / total) * 100;
                bar.style.width = pct + '%';
                bar.style.background = pct > 50 ? '#22C55E' : pct > 25 ? '#F97316' : '#ef4444';
                if (rem <= 30) timer.style.color = '#ef4444';
            }, 1000);
        }

        // ── Modal helpers ─────────────────────────────────
        function showQrModal(data) {
            document.getElementById('qrImageUSD').src = data.usd.qr_image;
            document.getElementById('qrImageKHR').src = data.khr.qr_image;
            document.getElementById('usdAmount').textContent = data.usd.label;
            document.getElementById('khrAmount').textContent = data.khr.label;
            document.getElementById('exchangeNote').textContent =
                `Exchange rate: $1 = ${Math.round(data.khr.amount / data.usd.amount).toLocaleString()} ៛`;
            document.getElementById('statusWaiting').style.display = 'block';
            document.getElementById('statusSuccess').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'none';
            document.getElementById('countdownArea').style.display = 'block';
            document.getElementById('cancelQrBtn').style.display = 'block';
            document.getElementById('countdownTimer').style.color = '#F97316';
            document.getElementById('countdownBar').style.width = '100%';
            document.getElementById('countdownBar').style.background = '#22C55E';
            document.getElementById('qrBackdrop').classList.add('open');
        }

        function showSuccessState(currency) {
            document.getElementById('statusWaiting').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'none';
            document.getElementById('statusSuccess').style.display = 'block';
            document.getElementById('countdownArea').style.display = 'none';
            document.getElementById('cancelQrBtn').style.display = 'none';
            document.getElementById('successCurrency').textContent =
                `Paid with ${currency === 'KHR' ? '🇰🇭 Khmer Riel (KHR)' : '🇺🇸 US Dollar (USD)'}`;
        }

        function showExpiredState() {
            document.getElementById('statusWaiting').style.display = 'none';
            document.getElementById('statusSuccess').style.display = 'none';
            document.getElementById('statusExpired').style.display = 'block';
            document.getElementById('countdownArea').style.display = 'none';
            document.getElementById('qrImageUSD').src = '';
            document.getElementById('qrImageKHR').src = '';
        }

        function closeQrPopup() {
            if (POS_STATE.paymentInterval) clearInterval(POS_STATE.paymentInterval);
            if (POS_STATE.countdownInterval) clearInterval(POS_STATE.countdownInterval);
            document.getElementById('qrBackdrop').classList.remove('open');
            document.getElementById('qrImageUSD').src = '';
            document.getElementById('qrImageKHR').src = '';
        }

        // ── Toast ─────────────────────────────────────────
        function showToast(msg, type = 'info') {
            const t = document.createElement('div');
            t.style.cssText =
                `position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;background:${type==='error'?'rgba(204,0,1,0.9)':'rgba(22,163,74,0.9)'};color:#fff;box-shadow:0 4px 20px rgba(0,0,0,0.4);`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }

        function pollAbaPayment(tranId) {
            if (POS_STATE.abaInterval) clearInterval(POS_STATE.abaInterval);
            let attempts = 0;
            POS_STATE.abaInterval = setInterval(async () => {   // ← store in POS_STATE
                attempts++;
                if (attempts > 60) {
                    clearInterval(POS_STATE.abaInterval);
                    showToast('ABA payment timed out', 'error');
                    closeAbaModal();
                    return;
                }
                try {
                    const res  = await fetch("{{ route('admin.pos.payway.verify') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        clearInterval(POS_STATE.abaInterval);
                        document.getElementById('abaWaiting').style.display = 'none';
                        document.getElementById('abaSuccess').style.display = 'block';
                        setTimeout(() => { window.location.href = data.receipt_url; }, 1500);
                    }
                } catch (e) {}
            }, 5000);
        }
        
        function showAbaModal(data) {
            const qrSrc = data.qr_image ||
                `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(data.qr_string)}`;
            document.getElementById('abaQrImage').src = qrSrc;
            document.getElementById('abaAmount').textContent = '$' + parseFloat(POS_STATE.totalAmount).toFixed(2);
            document.getElementById('abaWaiting').style.display = 'flex';
            document.getElementById('abaSuccess').style.display = 'none';
            // ✅ show using flex so centering works
            const bd = document.getElementById('abaBackdrop');
            bd.style.display = 'flex';
        }

        function closeAbaModal() {
            if (POS_STATE.abaInterval) clearInterval(POS_STATE.abaInterval);
            document.getElementById('abaBackdrop').style.display = 'none';
            document.getElementById('abaQrImage').src = '';
        }
    </script>
@endpush
