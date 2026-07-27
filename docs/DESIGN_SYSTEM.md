# MASJIDCMS DESIGN SYSTEM — UNIFIED ADMIN UI & UX STANDARD RC1

---

## 1. Executive Summary & Design Principles
- **System Name**: MasjidCMS Design System (M-DS RC1)
- **Target Platform**: Admin Dashboard & Public Website Interfaces
- **Design Philosophy**: **Clean Admin, Minimalist, Professional Enterprise SaaS**
- **Core Pillars**:
  1. **Content-First**: High clarity, minimal visual noise, data readability prioritized.
  2. **Consistent Ergonomics**: Standardized spacing, typography, and interactive component patterns across all modules.
  3. **Fast Navigation**: Clear visual hierarchy, accessible keyboard navigation, and responsive layout scaling.
  4. **Syariah & Professional Tone**: Primary color palette inspired by modern Islamic aesthetics (Emerald Green) combined with sleek dark/light neutral surfaces.

---

## 2. Design Tokens

### 2.1 Typography Token System
- **Primary Font Family**: `'Inter', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif`
- **Monospace Font Family**: `'Fira Code', 'JetBrains Mono', monospace` (used for Account Codes, Transaction Numbers, Journal Numbers)

| Token Name | Font Size | Line Height | Font Weight | Usage |
| :--- | :--- | :--- | :--- | :--- |
| `font-xs` | 0.75rem (12px) | 1.0rem (16px) | Regular (400) / Medium (500) | Badges, Table Headers, Help Text |
| `font-sm` | 0.875rem (14px)| 1.25rem (20px)| Regular (400) / Medium (500) | Form Labels, Table Cells, Body Text |
| `font-base` | 1.00rem (16px) | 1.50rem (24px)| Regular (400) / Medium (500) | Standard Inputs, Card Body |
| `font-lg` | 1.125rem (18px)| 1.75rem (28px)| SemiBold (600) | Section Headers, Subtitles |
| `font-xl` | 1.25rem (20px) | 1.75rem (28px)| SemiBold (600) / Bold (700) | Modal Titles, Card Headers |
| `font-2xl`| 1.50rem (24px) | 2.00rem (32px)| Bold (700) | Page Headers, Main Stat Figures |
| `font-3xl`| 1.875rem (30px)| 2.25rem (36px)| ExtraBold (800) | Metric Hero Displays |

---

### 2.2 Color System (Semantic Color Tokens)

#### A. Brand & Primary Accent (Emerald Syariah Green)
- `primary-50`  : `#ECFDF5` (Background subtle tint)
- `primary-100` : `#D1FAE5` (Badge background)
- `primary-500` : `#10B981` (Interactive primary)
- `primary-600` : `#059669` (Primary Hover State / Default Buttons)
- `primary-700` : `#047857` (Primary Active State)
- `primary-900` : `#064E3B` (Primary Deep Text)

#### B. Neutrals & Surface Colors
- `surface-light`  : `#FFFFFF` (Card & Modal Surface)
- `background-light`: `#F9FAFB` (Dashboard App Background)
- `border-light`    : `#E5E7EB` (Subtle Dividers & Card Outlines)
- `text-main`       : `#111827` (Primary Heading Text)
- `text-muted`      : `#4B5563` (Body & Description Text)
- `text-subtle`     : `#9CA3AF` (Placeholders & Disabled Text)

#### C. Functional Status Colors
- **Success (Green)** : `bg: #DCFCE7`, `text: #166534`, `border: #86EFAC` (Posted, Approved)
- **Warning (Amber)** : `bg: #FEF3C7`, `text: #92400E`, `border: #FCD34D` (Pending Approval, Draft)
- **Danger (Red)**    : `bg: #FEE2E2`, `text: #991B1B`, `border: #FCA5A5` (Rejected, Void, Lockout)
- **Info (Blue)**      : `bg: #E0F2FE`, `text: #075985`, `border: #7DD3FC` (System Notes, Activity)

---

### 2.3 Spacing Scale & Elevation
- **Base Grid Unit**: 4px (`0.25rem`)
- **Scale**: `p-1 (4px)`, `p-2 (8px)`, `p-3 (12px)`, `p-4 (16px)`, `p-6 (24px)`, `p-8 (32px)`, `p-12 (48px)`

#### Border Radius Tokens
- `radius-sm` : `4px` (Badges, Chips)
- `radius-md` : `6px` (Buttons, Form Control Inputs)
- `radius-lg` : `8px` (Cards, Modals, Panels)
- `radius-full`: `9999px` (Avatars, Circular Action Badges)

#### Shadows & Elevation
- `shadow-sm` : `0 1px 2px 0 rgba(0, 0, 0, 0.05)` (Cards, Small Dropdowns)
- `shadow-md` : `0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)` (Popovers, Modals)
- `shadow-lg` : `0 10px 15px -3px rgba(0, 0, 0, 0.1)` (Floating Drawers)

---

### 2.4 Grid System & Responsive Breakpoints

| Breakpoint Token | Min Width | Target Device | Layout Behavior |
| :--- | :--- | :--- | :--- |
| `sm` | `640px` | Mobile Landscape / Small Tablets | Single column stat grid, stacked forms |
| `md` | `768px` | Tablets | 2-column stat grid, collapsible sidebar |
| `lg` | `1024px`| Laptops / Desktops | Fixed 240px sidebar, 4-column stat grid |
| `xl` | `1280px`| Large Displays | Full multi-column dashboard workspace |

---

## 3. Component Library Catalog

### 3.1 Buttons & Action Controls
- **Primary Button**: `bg-primary-600 hover:bg-primary-700 text-white rounded-md px-4 py-2 text-sm font-medium shadow-sm transition`
- **Secondary Button**: `bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-md px-4 py-2 text-sm font-medium shadow-sm`
- **Danger Button**: `bg-red-600 hover:bg-red-700 text-white rounded-md px-4 py-2 text-sm font-medium`
- **Icon Button**: `p-2 rounded-md hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500`

### 3.2 Badges & Status Indicators
- `Draft`: `bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full font-medium`
- `Pending Approval`: `bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full font-medium`
- `Approved / Posted`: `bg-emerald-100 text-emerald-800 text-xs px-2.5 py-0.5 rounded-full font-medium`
- `Rejected / Void`: `bg-red-100 text-red-800 text-xs px-2.5 py-0.5 rounded-full font-medium`

### 3.3 Cards & Statistic Widgets
- **Statistic Card**:
  - Top: Label + Icon (e.g. "Total Infaq Jumat").
  - Middle: Big Bold Figure (e.g. "Rp 12.500.000").
  - Bottom: Delta Indicator (+15% vs bulan lalu).
- **Panel Card**: White background surface with subtle `border-gray-200`, `shadow-sm`, and rounded corners (`radius-lg`).

### 3.4 Data Display & Feedback Components
- **Modal Dialog**: Centered backdrop `bg-gray-900/50`, card body `bg-white rounded-lg p-6 max-w-lg w-full`, header title, and action footer.
- **Drawer**: Slide-over panel from right `w-96 bg-white shadow-xl` for quick details view.
- **Empty State**: Centered icon + Title ("Belum ada data transaksi") + Description + Primary Action Button ("+ Buat Transaksi").
- **Skeleton Loader**: Animated pulse `animate-pulse bg-gray-200 rounded` placeholders during async loading.

---

## 4. Form & Input Standard

- **Standard Text Input**:
  ```html
  <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jamaah <span class="text-red-500">*</span></label>
  <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Masukkan nama lengkap">
  <p class="mt-1 text-xs text-gray-500">Sesuai KTP / Kartu Keluarga</p>
  ```
- **Currency Input (Rupiah)**:
  - Left prefix addon: `Rp` in grey box.
  - Text alignment: Right-aligned numeric font.
- **Validation State**:
  - Error Input: `border-red-500 focus:ring-red-500` + Error help text `text-xs text-red-600 mt-1`.

---

## 5. Table Standard

- **Table Header**: `bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left border-b border-gray-200 py-3 px-4`
- **Table Row**: `hover:bg-gray-50 transition-colors border-b border-gray-100 py-3 px-4 text-sm text-gray-700`
- **Features Required**:
  - Search Input Bar at top left.
  - Date Range & Filter Dropdowns at top right.
  - Sortable column indicators (`▲` / `▼`).
  - Bulk Checkbox selection column.
  - Pagination Controls at bottom (`Menampilkan 1-10 dari 50 data`).

---

## 6. Dashboard Layout Architecture

```text
+-----------------------------------------------------------------------------------+
|  [Mosque Logo] MasjidCMS Admin    [Search...]     [Notifications] [User Profile] |  <- Top Header (64px)
+------------------+----------------------------------------------------------------+
|  📊 Dashboard    | Breadcrumb: Dashboard / Keuangan / Transaksi                   |
|  🕌 Masjid       | -------------------------------------------------------------- |  <- Content Header
|  👥 Jamaah       | [Header Title: Transaksi Keuangan]    [+ Buat Transaksi Baru]  |
|  👨‍👩‍👧 Family      | -------------------------------------------------------------- |
|  💰 Keuangan     | [Stat Card 1] [Stat Card 2] [Stat Card 3] [Stat Card 4]        |  <- Stat Cards Grid
|  📊 Laporan      | -------------------------------------------------------------- |
|  ⚙️ Pengaturan   | [Filter Bar: Search... | Fund Filter | Status Filter]          |
|                  | [Data Table: Transaction List                              ]  |  <- Main Workspace
|                  | [Pagination: < 1 2 3 >                                    ]  |
+------------------+----------------------------------------------------------------+
```

---

## 7. Iconography Standard

- **Standard Icon Library**: **Lucide Icons** / **Heroicons v2** (SVG line icons, 24x24px viewBox).
- **Icon Sizing Rules**:
  - `sm`: `16x16px` (Inline text / badges).
  - `md`: `20x20px` (Button icons, Sidebar items, Table action icons).
  - `lg`: `24x24px` (Card headers, Stat card icons).

---

## 8. Accessibility & WCAG 2.1 AA Checklist

- [x] **Color Contrast Ratio**: Minimum 4.5:1 for standard text, 3:1 for large headers.
- [x] **Focus Ring Visibility**: All interactive elements display clear `focus:ring-2 focus:ring-primary-500` on keyboard tab traversal.
- [x] **ARIA Attributes**: `aria-expanded`, `aria-hidden`, `aria-label`, `role="dialog"` applied to interactive elements.
- [x] **Keyboard Navigability**: Modals allow `Esc` key dismissal; tables support tab index focus.

---

## 9. Deliverable Summary & Roadmap Alignment

Document `docs/DESIGN_SYSTEM.md` serves as the binding UX/UI specification for:
- **TASK-051**: Admin Dashboard UX
- **TASK-052**: Master Data Workspace
- **TASK-053**: Financial Workspace
- **TASK-054**: Reporting Workspace
- **TASK-055**: Public Website
