import re

with open('resources/views/layouts/app.blade.php', 'r') as f:
    c = f.read()

# 1. Update Logo Size and container
# Increase logo height
c = c.replace('class="h-10 w-auto md:h-12 flex-shrink-0"', 'class="h-16 w-auto md:h-24 flex-shrink-0"')
# Also make the text slightly larger 
c = c.replace('text-sm md:text-base lg:text-lg font-bold', 'text-base md:text-lg lg:text-2xl font-bold')

# Change nav container from fixed height to padding
c = c.replace('<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 md:h-24 flex justify-between items-center relative">',
              '<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center relative">')

# 2. Move Desktop Menu
# Find the Desktop Menu block
desktop_menu_start = c.find('            <!-- Desktop Menu -->')
desktop_menu_end = c.find('            <!-- Language Toggle & Admin Button -->')

desktop_menu_block = c[desktop_menu_start:desktop_menu_end]

# Remove it from the original place
c = c[:desktop_menu_start] + c[desktop_menu_end:]

# 3. Create the Extra Header below the first nav
# We need to insert it right after the </nav> of the first header.
# Wait, there's a mobile menu sidebar which is inside the nav. 
# The </nav> is at the end of the header block.
nav_end = c.find('</nav>\n    </header>')
if nav_end == -1:
    nav_end = c.find('</nav>')

# The desktop menu block has `<div class="hidden lg:flex space-x-5 xl:space-x-10 items-center h-full">`
# I'll change `h-full` to `h-14` or `py-3` and add justification.
new_desktop_menu_block = desktop_menu_block.replace('class="hidden lg:flex space-x-5 xl:space-x-10 items-center h-full"',
                                                  'class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 hidden lg:flex space-x-8 xl:space-x-12 items-center justify-center py-3"')

# Actually, I'll wrap it in a border-t
extra_header = f"""
        </nav>
        
        <!-- Extra Nav Header for Links -->
        <div class="hidden lg:block border-t border-gray-100 shadow-sm relative z-40 bg-white" style="background-color: var(--header-bg); color: var(--header-text);">
{new_desktop_menu_block}
        </div>
"""

c = c[:nav_end] + extra_header + c[nav_end+len('</nav>'):]

with open('resources/views/layouts/app.blade.php', 'w') as f:
    f.write(c)

