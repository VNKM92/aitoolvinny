<?php

namespace App\Services;

use App\Services\SiteSettings;

class ThemeService
{
    public static function presets(): array
    {
        return [
            'default' => [
                'name' => 'Default',
                'values' => self::defaults(),
            ],
            'modern_blue' => [
                'name' => 'Modern Blue',
                'values' => array_merge(self::defaults(), [
                    'theme_primary' => '#2563eb',
                    'theme_primary_hover' => '#0ea5e9',
                    'theme_secondary' => '#0ea5e9',
                    'theme_accent' => '#8b5cf6',
                    'theme_success' => '#16a34a',
                    'theme_warning' => '#f59e0b',
                    'theme_danger' => '#ef4444',
                    'theme_info' => '#38bdf8',
                    'theme_body_bg' => '#020617',
                    'theme_body_text' => '#e2e8f0',
                    'theme_surface_bg' => '#0f172a',
                    'theme_section_bg' => '#111827',
                    'theme_header_bg' => '#0f172a',
                    'theme_header_text' => '#f8fafc',
                    'theme_footer_bg' => '#020617',
                    'theme_footer_text' => '#cbd5e1',
                    'theme_sidebar_bg' => '#111827',
                    'theme_sidebar_active' => '#2563eb',
                    'theme_sidebar_hover' => '#1e293b',
                    'theme_nav_color' => '#cbd5e1',
                    'theme_nav_hover' => '#ffffff',
                    'theme_border_color' => '#334155',
                    'theme_shadow_color' => 'rgba(15, 23, 42, 0.45)',
                    'theme_overlay_color' => 'rgba(15, 23, 42, 0.65)',
                    'theme_selection_color' => 'rgba(37, 99, 235, 0.25)',
                    'theme_scrollbar_color' => '#1e293b',
                    'theme_scrollbar_thumb_color' => '#334155',
                    'theme_font_heading' => "'Outfit', sans-serif",
                    'theme_font_body' => "'Inter', sans-serif",
                    'theme_font_button' => "'Inter', sans-serif",
                    'theme_font_menu' => "'Inter', sans-serif",
                    'theme_font_admin' => "'Inter', sans-serif",
                    'theme_font_size_base' => '16px',
                    'theme_line_height_base' => '1.6',
                    'theme_letter_spacing' => '0.01em',
                    'theme_font_weight_normal' => '400',
                    'theme_font_weight_semibold' => '600',
                    'theme_font_weight_bold' => '700',
                    'theme_button_radius' => '0.75rem',
                    'theme_button_padding' => '0.95rem 1.4rem',
                    'theme_button_transition' => '200ms ease',
                    'theme_card_radius' => '1rem',
                    'theme_card_shadow' => '0 20px 50px rgba(15, 23, 42, 0.15)',
                    'theme_card_hover_shadow' => '0 24px 60px rgba(15, 23, 42, 0.2)',
                    'theme_card_hover_scale' => '1.01',
                    'theme_card_padding' => '1.5rem',
                    'theme_form_input_bg' => '#0f172a',
                    'theme_form_input_border' => '#334155',
                    'theme_form_placeholder' => '#94a3b8',
                    'theme_form_focus_border' => '#2563eb',
                    'theme_form_focus_shadow' => '0 0 0 3px rgba(37, 99, 235, 0.12)',
                    'theme_form_label' => '#cbd5e1',
                    'theme_form_error' => '#f87171',
                    'theme_form_success' => '#34d399',
                    'theme_form_radius' => '0.75rem',
                    'theme_table_header_bg' => '#0f172a',
                    'theme_table_header_text' => '#e2e8f0',
                    'theme_table_row_bg' => '#111827',
                    'theme_table_row_alt' => '#0f172a',
                    'theme_table_row_hover' => '#1e293b',
                    'theme_table_border' => '#334155',
                    'theme_admin_sidebar_bg' => '#020617',
                    'theme_admin_sidebar_text' => '#cbd5e1',
                    'theme_admin_sidebar_active' => '#2563eb',
                    'theme_admin_sidebar_hover' => '#1e293b',
                    'theme_admin_navbar_bg' => '#0f172a',
                    'theme_admin_cards_bg' => '#111827',
                    'theme_admin_buttons_bg' => '#2563eb',
                    'theme_admin_forms_bg' => '#0f172a',
                    'theme_admin_tables_bg' => '#111827',
                    'theme_admin_widgets_bg' => '#111827',
                    'theme_admin_body_bg' => '#020617',
                    'theme_admin_body_text' => '#e2e8f0',
                    'theme_dark_mode' => 'auto',
                    'theme_dark_body_bg' => '#020617',
                    'theme_dark_surface_bg' => '#0f172a',
                    'theme_dark_card_bg' => '#111827',
                    'theme_dark_header_bg' => '#0f172a',
                    'theme_dark_footer_bg' => '#020617',
                    'theme_dark_buttons_bg' => '#2563eb',
                    'theme_dark_forms_bg' => '#0f172a',
                    'theme_dark_sidebar_bg' => '#020617',
                    'theme_dark_text' => '#e2e8f0',
                ]),
            ],
            'minimal_white' => [
                'name' => 'Minimal White',
                'values' => array_merge(self::defaults(), [
                    'theme_primary' => '#2563eb',
                    'theme_primary_hover' => '#4f46e5',
                    'theme_secondary' => '#64748b',
                    'theme_accent' => '#14b8a6',
                    'theme_success' => '#16a34a',
                    'theme_warning' => '#f59e0b',
                    'theme_danger' => '#dc2626',
                    'theme_info' => '#0ea5e9',
                    'theme_body_bg' => '#f8fafc',
                    'theme_body_text' => '#0f172a',
                    'theme_surface_bg' => '#ffffff',
                    'theme_section_bg' => '#f1f5f9',
                    'theme_header_bg' => '#ffffff',
                    'theme_header_text' => '#0f172a',
                    'theme_footer_bg' => '#ffffff',
                    'theme_footer_text' => '#475569',
                    'theme_sidebar_bg' => '#ffffff',
                    'theme_sidebar_active' => '#2563eb',
                    'theme_sidebar_hover' => '#e2e8f0',
                    'theme_nav_color' => '#0f172a',
                    'theme_nav_hover' => '#2563eb',
                    'theme_border_color' => '#e2e8f0',
                    'theme_shadow_color' => 'rgba(15, 23, 42, 0.08)',
                    'theme_overlay_color' => 'rgba(15, 23, 42, 0.14)',
                    'theme_selection_color' => 'rgba(37, 99, 235, 0.12)',
                    'theme_scrollbar_color' => '#e2e8f0',
                    'theme_scrollbar_thumb_color' => '#cbd5e1',
                    'theme_font_heading' => "'Inter', sans-serif",
                    'theme_font_body' => "'Roboto', sans-serif",
                    'theme_font_button' => "'Inter', sans-serif",
                    'theme_font_menu' => "'Inter', sans-serif",
                    'theme_font_admin' => "'Inter', sans-serif",
                    'theme_font_size_base' => '16px',
                    'theme_line_height_base' => '1.65',
                    'theme_letter_spacing' => '0.01em',
                    'theme_font_weight_normal' => '400',
                    'theme_font_weight_semibold' => '600',
                    'theme_font_weight_bold' => '700',
                    'theme_button_radius' => '0.75rem',
                    'theme_button_padding' => '0.95rem 1.4rem',
                    'theme_button_transition' => '180ms ease',
                    'theme_card_radius' => '1rem',
                    'theme_card_shadow' => '0 25px 60px rgba(15, 23, 42, 0.08)',
                    'theme_card_hover_shadow' => '0 30px 70px rgba(15, 23, 42, 0.12)',
                    'theme_card_hover_scale' => '1.01',
                    'theme_card_padding' => '1.5rem',
                    'theme_form_input_bg' => '#ffffff',
                    'theme_form_input_border' => '#e2e8f0',
                    'theme_form_placeholder' => '#94a3b8',
                    'theme_form_focus_border' => '#2563eb',
                    'theme_form_focus_shadow' => '0 0 0 3px rgba(37, 99, 235, 0.12)',
                    'theme_form_label' => '#475569',
                    'theme_form_error' => '#dc2626',
                    'theme_form_success' => '#16a34a',
                    'theme_form_radius' => '0.75rem',
                    'theme_table_header_bg' => '#f8fafc',
                    'theme_table_header_text' => '#0f172a',
                    'theme_table_row_bg' => '#ffffff',
                    'theme_table_row_alt' => '#f1f5f9',
                    'theme_table_row_hover' => '#e2e8f0',
                    'theme_table_border' => '#e2e8f0',
                    'theme_admin_sidebar_bg' => '#ffffff',
                    'theme_admin_sidebar_text' => '#0f172a',
                    'theme_admin_sidebar_active' => '#2563eb',
                    'theme_admin_sidebar_hover' => '#e2e8f0',
                    'theme_admin_navbar_bg' => '#ffffff',
                    'theme_admin_cards_bg' => '#ffffff',
                    'theme_admin_buttons_bg' => '#2563eb',
                    'theme_admin_forms_bg' => '#f8fafc',
                    'theme_admin_tables_bg' => '#ffffff',
                    'theme_admin_widgets_bg' => '#ffffff',
                    'theme_admin_body_bg' => '#ffffff',
                    'theme_admin_body_text' => '#0f172a',
                    'theme_dark_mode' => 'light',
                    'theme_dark_body_bg' => '#0f172a',
                    'theme_dark_surface_bg' => '#111827',
                    'theme_dark_card_bg' => '#111827',
                    'theme_dark_header_bg' => '#0f172a',
                    'theme_dark_footer_bg' => '#020617',
                    'theme_dark_buttons_bg' => '#2563eb',
                    'theme_dark_forms_bg' => '#0f172a',
                    'theme_dark_sidebar_bg' => '#020617',
                    'theme_dark_text' => '#e2e8f0',
                ]),
            ],
            'news_grate' => [
                'name' => 'News & Grate (Lightweight)',
                'values' => array_merge(self::defaults(), [
                    'theme_primary' => '#dc2626',
                    'theme_primary_hover' => '#b91c1c',
                    'theme_secondary' => '#1e3a8a',
                    'theme_accent' => '#d97706',
                    'theme_success' => '#16a34a',
                    'theme_warning' => '#ca8a04',
                    'theme_danger' => '#dc2626',
                    'theme_info' => '#0284c7',
                    'theme_body_bg' => '#ffffff',
                    'theme_body_text' => '#18181b',
                    'theme_body_bg_alt' => '#fafafa',
                    'theme_body_link_color' => '#dc2626',
                    'theme_body_link_hover' => '#991b1b',
                    'theme_body_heading_color' => '#09090b',
                    'theme_surface_bg' => '#ffffff',
                    'theme_card_bg' => '#ffffff',
                    'theme_section_bg' => '#f4f4f5',
                    'theme_header_bg' => '#ffffff',
                    'theme_header_text' => '#09090b',
                    'theme_footer_bg' => '#09090b',
                    'theme_footer_text' => '#a1a1aa',
                    'theme_sidebar_bg' => '#ffffff',
                    'theme_sidebar_active' => '#dc2626',
                    'theme_sidebar_hover' => '#f4f4f5',
                    'theme_nav_color' => '#27272a',
                    'theme_nav_hover' => '#dc2626',
                    'theme_border_color' => '#e4e4e7',
                    'theme_shadow_color' => 'rgba(0, 0, 0, 0.05)',
                    'theme_overlay_color' => 'rgba(9, 9, 11, 0.5)',
                    'theme_selection_color' => 'rgba(220, 38, 38, 0.12)',
                    'theme_scrollbar_color' => '#f4f4f5',
                    'theme_scrollbar_thumb_color' => '#a1a1aa',
                    'theme_font_heading' => "'Playfair Display', 'Georgia', serif",
                    'theme_font_body' => "'Source Serif Pro', 'Georgia', serif",
                    'theme_font_button' => "'Inter', sans-serif",
                    'theme_font_menu' => "'Inter', sans-serif",
                    'theme_font_admin' => "'Inter', sans-serif",
                    'theme_font_size_base' => '17px',
                    'theme_line_height_base' => '1.7',
                    'theme_letter_spacing' => '0',
                    'theme_font_weight_normal' => '400',
                    'theme_font_weight_semibold' => '600',
                    'theme_font_weight_bold' => '700',
                    'theme_button_radius' => '0px',
                    'theme_button_padding' => '0.75rem 1.25rem',
                    'theme_button_transition' => '150ms ease',
                    'theme_card_radius' => '0px',
                    'theme_card_shadow' => 'none',
                    'theme_card_hover_shadow' => '0 1px 2px rgba(0,0,0,0.05)',
                    'theme_card_hover_scale' => '1.00',
                    'theme_card_padding' => '1.25rem',
                    'theme_form_input_bg' => '#ffffff',
                    'theme_form_input_border' => '#d4d4d8',
                    'theme_form_placeholder' => '#71717a',
                    'theme_form_focus_border' => '#dc2626',
                    'theme_form_focus_shadow' => '0 0 0 2px rgba(220, 38, 38, 0.08)',
                    'theme_form_label' => '#3f3f46',
                    'theme_form_error' => '#b91c1c',
                    'theme_form_success' => '#15803d',
                    'theme_form_radius' => '0px',
                    'theme_table_header_bg' => '#fafafa',
                    'theme_table_header_text' => '#09090b',
                    'theme_table_row_bg' => '#ffffff',
                    'theme_table_row_alt' => '#fafafa',
                    'theme_table_row_hover' => '#f4f4f5',
                    'theme_table_border' => '#e4e4e7',
                    'theme_admin_sidebar_bg' => '#09090b',
                    'theme_admin_sidebar_text' => '#a1a1aa',
                    'theme_admin_sidebar_active' => '#dc2626',
                    'theme_admin_sidebar_hover' => '#18181b',
                    'theme_admin_navbar_bg' => '#ffffff',
                    'theme_admin_cards_bg' => '#ffffff',
                    'theme_admin_buttons_bg' => '#dc2626',
                    'theme_admin_forms_bg' => '#fafafa',
                    'theme_admin_tables_bg' => '#ffffff',
                    'theme_admin_widgets_bg' => '#fafafa',
                    'theme_admin_body_bg' => '#fafafa',
                    'theme_admin_body_text' => '#18181b',
                    'theme_dark_mode' => 'light',
                    'theme_dark_body_bg' => '#09090b',
                    'theme_dark_body_text' => '#e4e4e7',
                    'theme_dark_surface_bg' => '#18181b',
                    'theme_dark_card_bg' => '#18181b',
                    'theme_dark_header_bg' => '#18181b',
                    'theme_dark_footer_bg' => '#09090b',
                    'theme_dark_buttons_bg' => '#dc2626',
                    'theme_dark_forms_bg' => '#18181b',
                    'theme_dark_sidebar_bg' => '#09090b',
                    'theme_dark_text' => '#e4e4e7',
                ]),
            ],
            'tech_magazine' => [
                'name' => 'Tech Magazine (Clean)',
                'values' => array_merge(self::defaults(), [
                    'theme_primary' => '#2563eb',
                    'theme_primary_hover' => '#1d4ed8',
                    'theme_secondary' => '#0891b2',
                    'theme_accent' => '#f59e0b',
                    'theme_success' => '#10b981',
                    'theme_warning' => '#f59e0b',
                    'theme_danger' => '#ef4444',
                    'theme_info' => '#06b6d4',
                    'theme_body_bg' => '#f8fafc',
                    'theme_body_text' => '#1e293b',
                    'theme_body_bg_alt' => '#ffffff',
                    'theme_body_link_color' => '#2563eb',
                    'theme_body_link_hover' => '#1e40af',
                    'theme_body_heading_color' => '#0f172a',
                    'theme_surface_bg' => '#ffffff',
                    'theme_card_bg' => '#ffffff',
                    'theme_section_bg' => '#f1f5f9',
                    'theme_header_bg' => 'rgba(255, 255, 255, 0.98)',
                    'theme_header_text' => '#0f172a',
                    'theme_footer_bg' => '#0f172a',
                    'theme_footer_text' => '#94a3b8',
                    'theme_sidebar_bg' => '#ffffff',
                    'theme_sidebar_active' => '#2563eb',
                    'theme_sidebar_hover' => '#f1f5f9',
                    'theme_nav_color' => '#334155',
                    'theme_nav_hover' => '#2563eb',
                    'theme_border_color' => 'rgba(15, 23, 42, 0.1)',
                    'theme_shadow_color' => 'rgba(15, 23, 42, 0.06)',
                    'theme_overlay_color' => 'rgba(15, 23, 42, 0.45)',
                    'theme_selection_color' => 'rgba(37, 99, 235, 0.14)',
                    'theme_scrollbar_color' => '#e2e8f0',
                    'theme_scrollbar_thumb_color' => '#94a3b8',
                    'theme_font_heading' => "'Outfit', 'Inter', sans-serif",
                    'theme_font_body' => "'Inter', sans-serif",
                    'theme_font_button' => "'Inter', sans-serif",
                    'theme_font_menu' => "'Inter', sans-serif",
                    'theme_font_admin' => "'Inter', sans-serif",
                    'theme_font_size_base' => '16px',
                    'theme_line_height_base' => '1.65',
                    'theme_letter_spacing' => '0.005em',
                    'theme_font_weight_normal' => '400',
                    'theme_font_weight_semibold' => '600',
                    'theme_font_weight_bold' => '700',
                    'theme_button_radius' => '0.375rem',
                    'theme_button_padding' => '0.75rem 1.25rem',
                    'theme_button_transition' => '150ms ease',
                    'theme_card_radius' => '0.5rem',
                    'theme_card_shadow' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                    'theme_card_hover_shadow' => '0 4px 12px rgba(15, 23, 42, 0.08)',
                    'theme_card_hover_scale' => '1.00',
                    'theme_card_padding' => '1.25rem',
                    'theme_form_input_bg' => '#ffffff',
                    'theme_form_input_border' => '#cbd5e1',
                    'theme_form_placeholder' => '#94a3b8',
                    'theme_form_focus_border' => '#2563eb',
                    'theme_form_focus_shadow' => '0 0 0 3px rgba(37, 99, 235, 0.1)',
                    'theme_form_label' => '#475569',
                    'theme_form_error' => '#dc2626',
                    'theme_form_success' => '#16a34a',
                    'theme_form_radius' => '0.375rem',
                    'theme_table_header_bg' => '#f8fafc',
                    'theme_table_header_text' => '#0f172a',
                    'theme_table_row_bg' => '#ffffff',
                    'theme_table_row_alt' => '#f8fafc',
                    'theme_table_row_hover' => '#f1f5f9',
                    'theme_table_border' => '#e2e8f0',
                    'theme_admin_sidebar_bg' => '#0f172a',
                    'theme_admin_sidebar_text' => '#cbd5e1',
                    'theme_admin_sidebar_active' => '#2563eb',
                    'theme_admin_sidebar_hover' => '#1e293b',
                    'theme_admin_navbar_bg' => '#ffffff',
                    'theme_admin_cards_bg' => '#ffffff',
                    'theme_admin_buttons_bg' => '#2563eb',
                    'theme_admin_forms_bg' => '#f8fafc',
                    'theme_admin_tables_bg' => '#ffffff',
                    'theme_admin_widgets_bg' => '#f8fafc',
                    'theme_admin_body_bg' => '#f1f5f9',
                    'theme_admin_body_text' => '#0f172a',
                    'theme_dark_mode' => 'light',
                    'theme_dark_body_bg' => '#0f172a',
                    'theme_dark_body_text' => '#e2e8f0',
                    'theme_dark_surface_bg' => '#1e293b',
                    'theme_dark_card_bg' => '#1e293b',
                    'theme_dark_header_bg' => '#0f172a',
                    'theme_dark_footer_bg' => '#020617',
                    'theme_dark_buttons_bg' => '#2563eb',
                    'theme_dark_forms_bg' => '#1e293b',
                    'theme_dark_sidebar_bg' => '#020617',
                    'theme_dark_text' => '#e2e8f0',
                ]),
            ],
        ];
    }

    public static function defaults(): array
    {
        return [
            'theme_primary' => '#4f46e5',
            'theme_primary_hover' => '#4338ca',
            'theme_secondary' => '#64748b',
            'theme_accent' => '#ec4899',
            'theme_success' => '#22c55e',
            'theme_warning' => '#f59e0b',
            'theme_danger' => '#ef4444',
            'theme_info' => '#38bdf8',
            'theme_body_bg' => '#f8fafc',
            'theme_body_text' => '#0f172a',
            'theme_body_bg_alt' => '#f1f5f9',
            'theme_body_link_color' => '#2563eb',
            'theme_body_link_hover' => '#1d4ed8',
            'theme_body_heading_color' => '#0f172a',
            'theme_surface_bg' => '#ffffff',
            'theme_card_bg' => 'rgba(255, 255, 255, 0.95)',
            'theme_section_bg' => '#f1f5f9',
            'theme_header_bg' => 'rgba(255, 255, 255, 0.96)',
            'theme_header_text' => '#0f172a',
            'theme_footer_bg' => '#0f172a',
            'theme_footer_text' => '#cbd5e1',
            'theme_sidebar_bg' => '#ffffff',
            'theme_sidebar_active' => '#4f46e5',
            'theme_sidebar_hover' => '#f1f5f9',
            'theme_nav_color' => '#334155',
            'theme_nav_hover' => '#4f46e5',
            'theme_border_color' => 'rgba(15, 23, 42, 0.08)',
            'theme_shadow_color' => 'rgba(15, 23, 42, 0.08)',
            'theme_overlay_color' => 'rgba(15, 23, 42, 0.5)',
            'theme_selection_color' => 'rgba(79, 70, 229, 0.15)',
            'theme_scrollbar_color' => '#e2e8f0',
            'theme_scrollbar_thumb_color' => '#94a3b8',
            'theme_font_heading' => "'Playfair Display', 'Outfit', sans-serif",
            'theme_font_body' => "'Inter', sans-serif",
            'theme_font_button' => "'Inter', sans-serif",
            'theme_font_menu' => "'Inter', sans-serif",
            'theme_font_admin' => "'Inter', sans-serif",
            'theme_font_size_base' => '16px',
            'theme_line_height_base' => '1.65',
            'theme_letter_spacing' => '0.01em',
            'theme_font_weight_normal' => '400',
            'theme_font_weight_semibold' => '600',
            'theme_font_weight_bold' => '700',
            'theme_button_radius' => '0.5rem',
            'theme_button_padding' => '0.875rem 1.25rem',
            'theme_button_transition' => '180ms ease',
            'theme_card_radius' => '0.75rem',
            'theme_card_shadow' => '0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04)',
            'theme_card_hover_shadow' => '0 10px 25px rgba(15, 23, 42, 0.08), 0 4px 10px rgba(15, 23, 42, 0.04)',
            'theme_card_hover_scale' => '1.00',
            'theme_card_padding' => '1.5rem',
            'theme_form_input_bg' => '#ffffff',
            'theme_form_input_border' => '#e2e8f0',
            'theme_form_placeholder' => '#94a3b8',
            'theme_form_focus_border' => '#4f46e5',
            'theme_form_focus_shadow' => '0 0 0 3px rgba(79, 70, 229, 0.1)',
            'theme_form_label' => '#334155',
            'theme_form_error' => '#dc2626',
            'theme_form_success' => '#16a34a',
            'theme_form_radius' => '0.5rem',
            'theme_table_header_bg' => '#f8fafc',
            'theme_table_header_text' => '#0f172a',
            'theme_table_row_bg' => '#ffffff',
            'theme_table_row_alt' => '#f8fafc',
            'theme_table_row_hover' => '#f1f5f9',
            'theme_table_border' => '#e2e8f0',
            'theme_admin_sidebar_bg' => '#0f172a',
            'theme_admin_sidebar_text' => '#cbd5e1',
            'theme_admin_sidebar_active' => '#4f46e5',
            'theme_admin_sidebar_hover' => '#1e293b',
            'theme_admin_navbar_bg' => '#ffffff',
            'theme_admin_cards_bg' => '#ffffff',
            'theme_admin_buttons_bg' => '#4f46e5',
            'theme_admin_forms_bg' => '#f8fafc',
            'theme_admin_tables_bg' => '#ffffff',
            'theme_admin_widgets_bg' => '#f8fafc',
            'theme_admin_body_bg' => '#f1f5f9',
            'theme_admin_body_text' => '#0f172a',
            'theme_backend_primary' => '#6366f1',
            'theme_backend_primary_hover' => '#4f46e5',
            'theme_dark_mode' => 'auto',
            'theme_dark_body_bg' => '#020617',
            'theme_dark_body_text' => '#e2e8f0',
            'theme_dark_surface_bg' => '#0f172a',
            'theme_dark_card_bg' => '#111827',
            'theme_dark_header_bg' => '#0f172a',
            'theme_dark_footer_bg' => '#020617',
            'theme_dark_buttons_bg' => '#4f46e5',
            'theme_dark_forms_bg' => '#0f172a',
            'theme_dark_sidebar_bg' => '#020617',
            'theme_dark_text' => '#e2e8f0',
        ];
    }

    public static function themeSettings(): array
    {
        $themeSettings = SiteSettings::get('theme_settings', []);
        $merged = array_merge(self::defaults(), is_array($themeSettings) ? $themeSettings : []);

        foreach (array_keys(self::defaults()) as $key) {
            $legacyValue = SiteSettings::get($key, null);
            if ($legacyValue !== null) {
                $merged[$key] = $legacyValue;
            }
        }

        return $merged;
    }

    public static function adminThemeSettings(): array
    {
        $themeSettings = self::themeSettings();
        $adminOverrides = SiteSettings::get('admin_theme_settings', []);

        if (!is_array($adminOverrides)) {
            $adminOverrides = [];
        }

        return array_merge($themeSettings, $adminOverrides);
    }

    public static function cssVariables(array $settings): string
    {
        $output = '';
        foreach ($settings as $key => $value) {
            $varName = '--' . str_replace('_', '-', $key);
            $output .= $varName . ': ' . $value . ";\n";
        }
        return trim($output);
    }

    public static function getEffectiveThemeSettings(?array $pageOverrides = null, bool $isDarkMode = false): array
    {
        $base = self::themeSettings();

        if ($isDarkMode) {
            $base['theme_body_bg'] = $base['theme_dark_body_bg'] ?? $base['theme_body_bg'];
            $base['theme_body_text'] = $base['theme_dark_text'] ?? $base['theme_body_text'];
            $base['theme_surface_bg'] = $base['theme_dark_surface_bg'] ?? $base['theme_surface_bg'];
            $base['theme_card_bg'] = $base['theme_dark_card_bg'] ?? $base['theme_card_bg'];
            $base['theme_header_bg'] = $base['theme_dark_header_bg'] ?? $base['theme_header_bg'];
            $base['theme_footer_bg'] = $base['theme_dark_footer_bg'] ?? $base['theme_footer_bg'];
            $base['theme_form_input_bg'] = $base['theme_dark_forms_bg'] ?? $base['theme_form_input_bg'];
            $base['theme_sidebar_bg'] = $base['theme_dark_sidebar_bg'] ?? $base['theme_sidebar_bg'];
        }

        if (is_array($pageOverrides) && !empty($pageOverrides)) {
            foreach ($pageOverrides as $key => $value) {
                if ($value !== null && $value !== '') {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }

    public static function getPageThemeOverrides(?object $pageOrPost = null): array
    {
        if (!$pageOrPost) {
            return [];
        }

        $overrides = [];

        if (isset($pageOrPost->theme_body_bg) && !empty($pageOrPost->theme_body_bg)) {
            $overrides['theme_body_bg'] = $pageOrPost->theme_body_bg;
        }
        if (isset($pageOrPost->theme_body_text) && !empty($pageOrPost->theme_body_text)) {
            $overrides['theme_body_text'] = $pageOrPost->theme_body_text;
        }
        if (isset($pageOrPost->theme_header_bg) && !empty($pageOrPost->theme_header_bg)) {
            $overrides['theme_header_bg'] = $pageOrPost->theme_header_bg;
        }
        if (isset($pageOrPost->theme_footer_bg) && !empty($pageOrPost->theme_footer_bg)) {
            $overrides['theme_footer_bg'] = $pageOrPost->theme_footer_bg;
        }
        if (isset($pageOrPost->theme_primary) && !empty($pageOrPost->theme_primary)) {
            $overrides['theme_primary'] = $pageOrPost->theme_primary;
        }
        if (isset($pageOrPost->theme_accent) && !empty($pageOrPost->theme_accent)) {
            $overrides['theme_accent'] = $pageOrPost->theme_accent;
        }
        if (isset($pageOrPost->theme_section_bg) && !empty($pageOrPost->theme_section_bg)) {
            $overrides['theme_section_bg'] = $pageOrPost->theme_section_bg;
        }
        if (isset($pageOrPost->theme_card_bg) && !empty($pageOrPost->theme_card_bg)) {
            $overrides['theme_card_bg'] = $pageOrPost->theme_card_bg;
        }

        return $overrides;
    }

    public static function bodyClasses(array $settings, ?string $extraClasses = null): string
    {
        $classes = [
            'min-h-full',
            'flex',
            'flex-col',
            'justify-between',
            'relative',
            'overflow-x-hidden',
            'transition-colors',
            'duration-200',
        ];

        if ($extraClasses) {
            $classes[] = $extraClasses;
        }

        return implode(' ', $classes);
    }

    public static function applyPreset(string $presetKey): void
    {
        $presets = self::presets();
        if (isset($presets[$presetKey])) {
            $values = $presets[$presetKey]['values'];
            SiteSettings::set('theme_settings', $values);

            foreach ($values as $k => $v) {
                if (str_starts_with($k, 'theme_')) {
                    SiteSettings::set($k, $v);
                }
            }

            if (isset($values['theme_admin_body_bg']) || isset($values['theme_admin_body_text'])) {
                $adminOverrides = [
                    'theme_admin_body_bg' => $values['theme_admin_body_bg'] ?? null,
                    'theme_admin_body_text' => $values['theme_admin_body_text'] ?? null,
                    'theme_backend_primary' => $values['theme_backend_primary'] ?? null,
                    'theme_backend_primary_hover' => $values['theme_backend_primary_hover'] ?? null,
                    'theme_admin_sidebar_bg' => $values['theme_admin_sidebar_bg'] ?? null,
                    'theme_admin_sidebar_text' => $values['theme_admin_sidebar_text'] ?? null,
                ];
                $adminOverrides = array_filter($adminOverrides, fn($v) => $v !== null);
                if (!empty($adminOverrides)) {
                    SiteSettings::set('admin_theme_settings', $adminOverrides);
                }
            }
        }
    }
}
