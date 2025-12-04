# Product Hierarchy Setup Guide

This guide explains how to set up product pages with parent/child relationships for non-technical users.

## Understanding Product Hierarchy

**Parent Products (Category Pages):**
- Top-level products that organize other products
- Example: "Industrial Doors" is a parent that contains "Fire Doors", "Security Doors", etc.
- When enabled, automatically displays all child products on the page

**Child Products:**
- Individual products that belong under a parent
- Example: "Fire Doors" is a child of "Industrial Doors"
- Will automatically appear on the parent page

---

## Step-by-Step Setup Process

### Step 1: Create Your Parent Product (Category Page)

1. **Go to Products → Add New** in WordPress admin
2. **Enter the product title** (e.g., "Industrial Doors")
3. **Add your content** (description, images, etc.)
4. **In the sidebar, find "Product Category Page Settings"**
   - Check the box: **"Yes, automatically display child products on this page"**
   - This tells WordPress this is a category page
5. **Look at the "Product Hierarchy" box** (right sidebar)
   - It will show "No Child Products" - that's normal for now
6. **Publish the page**

### Step 2: Create Child Products

1. **Go to Products → Add New** again
2. **Enter the child product title** (e.g., "Fire Doors")
3. **Add your content** (description, images, etc.)
4. **In the "Page Attributes" box** (right sidebar):
   - Find the **"Parent"** dropdown
   - Select your parent product (e.g., "Industrial Doors")
5. **Publish the page**
6. **Repeat** for each child product you want to add

### Step 3: Sync Products to Navigation Menu

After creating your product hierarchy, you need to sync them to the navigation menu so they appear in the main menu.

**How to Access the Sync Page:**

1. **Option 1: From Products List**
   - Go to **Products → All Products**
   - Look for the **"Sync Products to Menu"** button next to the "Add New" button
   - Click it to go to the sync page

2. **Option 2: From Submenu**
   - Go to **Products → Sync to Menu** (in the left sidebar under Products)

3. **Option 3: Direct URL**
   - Go to: `yoursite.com/wp-admin/edit.php?post_type=product&page=sync-products-menu`

**On the Sync Page:**
- Click the **"Sync Products to Menu"** button
- This will:
  - Create a "Products" menu item if it doesn't exist
  - Add all parent products (category pages) under "Products"
  - Add all child products under their parent in the menu
  - Show you a summary of what was synced

**Note:** You must have a menu assigned to the "Header" location first. Go to **Appearance → Menus** to set this up.

### Step 4: Verify the Hierarchy

1. **Edit your parent product** (e.g., "Industrial Doors")
2. **Check the "Product Hierarchy" box** (right sidebar)
   - You should now see all your child products listed
   - It will show: "✅ Category Page Mode: These child products will automatically display..."
3. **Edit any child product**
   - The "Product Hierarchy" box will show the parent product
   - It will say: "This product belongs under [Parent Name]"

### Step 4: Add Products to the Frontend

1. **Edit your parent product page** (the category page)
2. **Add a "Product Relationship" block** to the page
3. **The block will automatically show all child products** - no manual selection needed!
4. **If you want to filter by Product Type, Category, or Tags:**
   - Use the filter options in the block settings
   - This will show only child products matching those filters
5. **Publish/Update the page**

---

## Quick Reference

### Creating a Category Page (Parent)
- ✅ Create product
- ✅ Enable "Display Child Products Automatically"
- ✅ Publish

### Creating a Child Product
- ✅ Create product
- ✅ Set "Parent" in Page Attributes dropdown
- ✅ Publish

### Viewing Hierarchy
- ✅ Check "Product Hierarchy" box on any product
- ✅ View "Parent" and "Children" columns in Products list

### Using on Frontend
- ✅ Add "Product Relationship" block to parent page
- ✅ Child products appear automatically
- ✅ Optional: Add filters for Product Type/Category/Tags

---

## Common Questions

**Q: Do I need to manually select products in the Product Relationship block?**
A: No! If "Display Child Products Automatically" is enabled, children appear automatically.

**Q: Can a child product have its own children?**
A: Yes! Products can have multiple levels (grandparent → parent → child).

**Q: What if I want to show specific products instead of all children?**
A: Don't enable "Display Child Products Automatically" and use the manual "Products" field in the block instead.

**Q: How do I change a product's parent?**
A: Edit the product and change the "Parent" dropdown in Page Attributes.

**Q: How do I remove a product from a parent?**
A: Edit the product and set "Parent" to "— No Parent —" in Page Attributes.

---

## Visual Indicators

**In the Products List:**
- **Parent Column:** Shows parent product or "— Top Level —"
- **Children Column:** Shows count and names of child products
- **Category Page Column:** Shows "✅ Yes" if auto-display is enabled

**On Product Edit Screen:**
- **Product Hierarchy Box:** Shows parent/child relationships visually
- **Page Attributes:** "Parent" dropdown for setting relationships
- **Category Page Settings:** Toggle for auto-display feature

---

## Troubleshooting

**Problem: Child products not showing on parent page**
- ✅ Check "Display Child Products Automatically" is enabled on parent
- ✅ Verify child products have the correct parent set
- ✅ Make sure child products are published (not draft)
- ✅ Check the Product Relationship block is added to the page

**Problem: Can't see Parent dropdown**
- ✅ Make sure "Page Attributes" box is visible (check Screen Options)
- ✅ Products post type supports page attributes (should be automatic)

**Problem: Too many products showing**
- ✅ Use the filter options in Product Relationship block
- ✅ Filter by Product Type, Category, or Tags

---

## Best Practices

1. **Name your products clearly** - Makes hierarchy easier to understand
2. **Use consistent naming** - "Industrial Doors" → "Fire Doors" (not "Fire Door Product")
3. **Keep hierarchy simple** - 2-3 levels maximum is usually best
4. **Test on frontend** - Always preview to see how it looks
5. **Use filters wisely** - Only filter if you need to show specific products

---

## Example Structure

```
Industrial Doors (Parent - Category Page Enabled)
├── Fire Doors (Child)
├── Security Doors (Child)
└── Sliding Doors (Child)

Commercial Doors (Parent - Category Page Enabled)
├── Glass Doors (Child)
└── Metal Doors (Child)
```

Each parent automatically displays its children when you add a Product Relationship block!

