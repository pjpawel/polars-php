# Configuration file for the Sphinx documentation builder.

project = 'Polars-PHP'
copyright = '2025, Paweł Podgórski'
author = 'Paweł Podgórski'
release = '0.2.0'

extensions = [
    'myst_parser',
    'sphinx.ext.autodoc',
    'sphinx.ext.viewcode',
    'sphinx_copybutton',
]

templates_path = ['_templates']
exclude_patterns = ['_build', 'Thumbs.db', '.DS_Store']

html_theme = 'pydata_sphinx_theme'
html_static_path = ['_static']

html_theme_options = {
    "github_url": "https://github.com/pjpawel/polars-php",
    "show_toc_level": 2,
    "navigation_with_keys": True,
}

myst_enable_extensions = [
    "colon_fence",
    "deflist",
    "fieldlist",
]

# Support both .rst and .md
source_suffix = {
    '.rst': 'restructuredtext',
    '.md': 'markdown',
}
