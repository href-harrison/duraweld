# Mega Menu Setup Guide

## Overview
This theme includes a two-column mega menu system for the Products dropdown menu. The implementation uses CSS Grid for layout and minimal JavaScript for DOM organization.

## WordPress Menu Structure

The mega menu expects a standard WordPress nested menu structure:

```html
<nav class="site-header__navigation">
  <ul class="site-header__menu">
    <li class="menu-item menu-item-has-children mega-menu">
      <a href="/products">Products</a>
      <ul class="sub-menu">
        <!-- Column 1: Parent Categories -->
        <li class="menu-item">
          <a href="/products/ring-binders">Ring Binders</a>
        </li>
        <li class="menu-item highlighted-item">
          <a href="/products/swatch-books">Swatch Books</a>
        </li>
        <li class="menu-item">
          <a href="/products/pockets-wallets">Pockets & Wallets</a>
        </li>
        <!-- ... more parent categories ... -->
        
        <!-- Column 2: Child Products (automatically moved here by JavaScript) -->
        <li class="menu-item child-sub-menu-item">
          <a href="/products/promo-goods">Promo Goods</a>
        </li>
        <li class="menu-item child-sub-menu-item">
          <a href="/products/product-packaging">Product Packaging</a>
        </li>
        <!-- ... more child products ... -->
      </ul>
    </li>
    <li class="menu-item">
      <a href="/about">About</a>
    </li>
    <!-- ... other menu items ... -->
  </ul>
</nav>
```

## Setup Instructions

### 1. Add "mega-menu" Class to Products Menu Item

**Option A: Automatic (Recommended)**
The theme automatically adds the `mega-menu` class to menu items with the title "Products" or "Product". This is handled in `includes/lib/product-hierarchy.php`.

**Option B: Manual**
1. Go to **Appearance > Menus** in WordPress admin
2. Find the "Products" menu item
3. Click to expand it
4. In the **CSS Classes** field, add: `mega-menu`
5. Save the menu

### 2. Add "highlighted-item" Class (Optional)

To highlight a specific menu item (like "Swatch Books" in the design):

1. Go to **Appearance > Menus**
2. Find the menu item you want to highlight
3. Click to expand it
4. In the **CSS Classes** field, add: `highlighted-item`
5. Save the menu

### 3. Child Products Setup

Child products are automatically identified and styled via the `add_product_child_menu_class()` function, which:
- Checks if a menu item is a product post type
- Checks if that product has a parent product
- Adds the `child-sub-menu-item` class automatically

## CSS Classes Reference

### Required Classes

- **`.mega-menu`** - Applied to the parent "Products" menu item to enable mega menu styling
- **`.child-sub-menu-item`** - Automatically added to child product menu items (handled by PHP)

### Optional Classes

- **`.highlighted-item`** - Add manually to any menu item to give it a highlighted background

## Styling Details

### Layout
- **Two-column CSS Grid**: 250px × 250px columns
- **Column 1**: Parent product categories (left)
- **Column 2**: Child products (right)

### Colors
- **Background**: `#f8f8f8` (light grey)
- **Hover**: `#e5e5e5` (medium grey)
- **Text**: `#444` (dark grey)
- **Borders**: `#ccc` (light grey, dotted)

### Separators
- **Horizontal**: Dotted line between menu items (`border-bottom: 1px dotted #ccc`)
- **Vertical**: Dotted line between columns (`border-right: 1px dotted #ccc`)

## JavaScript Functionality

The JavaScript (`js/main.js`) performs minimal DOM reorganization:
1. Finds all mega menu containers
2. Separates parent categories from child products
3. Re-orders items: parents first, then children
4. CSS Grid handles the two-column layout automatically

## Customization

### Change Column Widths
Edit `scss/components/_header.scss`:
```scss
grid-template-columns: 250px 250px; // Change these values
```

### Change Colors
Edit the color values in `.mega-menu .sub-menu` styles:
```scss
background: #f8f8f8; // Main background
// Hover color: #e5e5e5
// Text color: #444
// Border color: #ccc
```

### Change Highlighted Item Behavior
Edit the `.highlighted-item` styles in the SCSS file.

## Troubleshooting

### Mega Menu Not Appearing
1. Ensure the `mega-menu` class is added to the Products menu item
2. Check that the menu item has children (sub-menu items)
3. Verify JavaScript is loading (check browser console)

### Child Products Not in Second Column
1. Ensure child products have the `child-sub-menu-item` class
2. Check that the PHP function `add_product_child_menu_class()` is working
3. Verify JavaScript is reorganizing the DOM correctly

### Styling Issues
1. Clear browser cache
2. Rebuild CSS (run `npm run build` if using build process)
3. Check for CSS conflicts with other theme styles

