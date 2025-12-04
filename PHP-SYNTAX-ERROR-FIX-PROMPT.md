# PHP Syntax Error Fix Prompt

Use this prompt template to fix PHP syntax errors like unclosed braces, brackets, parentheses, and other parse errors.

## Prompt Template

```
Fix all PHP syntax errors in my codebase, specifically errors like:

Fatal error: Uncaught Error: Unclosed '{' on line X in /path/to/file.php on line Y

Common PHP syntax errors to look for and fix:
1. Unclosed braces { }
2. Unclosed brackets [ ]
3. Unclosed parentheses ( )
4. Missing semicolons ;
5. Missing commas in arrays
6. Mismatched quotes
7. Unclosed anonymous functions/closures
8. Incomplete function calls

For each error found:
1. Read the file containing the error
2. Identify the exact location and type of syntax error
3. Fix the error by adding the missing closing character(s)
4. Verify the fix doesn't introduce new errors

After fixing, check for:
- All braces, brackets, and parentheses are properly matched
- All function calls are properly closed
- All array declarations are properly closed
- All anonymous functions have proper closing syntax (});
- All statements end with semicolons where required

Please scan through all PHP files systematically and fix any syntax errors found.
```

## Usage Instructions

1. **Copy the prompt above** and paste it when you encounter PHP syntax errors
2. **Or modify it** to be more specific to your error:
   - Replace `/path/to/file.php` with your actual file path
   - Specify the exact error message you're seeing
   - Add any additional context about what was recently changed

## Example Usage

```
Fix the PHP syntax error in my WordPress theme:

Error: Fatal error: Uncaught Error: Unclosed '{' on line 91 in /var/www/html/wp-content/themes/prima-branding/includes/lib/acf.php on line 93

Please:
1. Read the file wp-content/themes/prima-branding/includes/lib/acf.php
2. Identify the unclosed brace issue around line 91-93
3. Fix the syntax error by adding the missing closing characters
4. Verify the fix is correct by checking brace/bracket/parentheses matching
```

## Common PHP Syntax Error Patterns

### 1. Unclosed Anonymous Function
```php
// ❌ WRONG - Missing closing });
add_filter('hook_name', function($param) {
  return $param;
// Missing closing here

// ✅ CORRECT
add_filter('hook_name', function($param) {
  return $param;
});
```

### 2. Unclosed Array
```php
// ❌ WRONG
$array = [
  'key1' => 'value1',
  'key2' => 'value2'
// Missing closing ]

// ✅ CORRECT
$array = [
  'key1' => 'value1',
  'key2' => 'value2'
];
```

### 3. Unclosed Function
```php
// ❌ WRONG
function my_function() {
  // code here
// Missing closing }

// ✅ CORRECT
function my_function() {
  // code here
}
```

### 4. Unclosed If Statement
```php
// ❌ WRONG
if ($condition) {
  // code
// Missing closing }

// ✅ CORRECT
if ($condition) {
  // code
}
```

## Tips for Identifying Syntax Errors

1. **Check the error message** - It usually indicates the line number and type of error
2. **Count braces/brackets** - They should match in pairs
3. **Look at recent changes** - Syntax errors often occur after editing code
4. **Use an IDE** - Most IDEs highlight unmatched braces/brackets
5. **Run PHP lint** - Use `php -l filename.php` to check syntax

## Automated Fix Process

When using this prompt, the AI should:

1. ✅ Read the problematic file
2. ✅ Analyze the syntax error
3. ✅ Count and match all braces, brackets, and parentheses
4. ✅ Identify what's missing
5. ✅ Add the missing closing characters
6. ✅ Verify the fix is correct
7. ✅ Check for any other syntax errors in the file

