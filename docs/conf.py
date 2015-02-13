# -*- coding: utf-8 -*-
#
import os
import sys
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

extensions = [
    'sphinx.ext.autodoc',
    'sphinx.ext.intersphinx',
    'sphinx_http_domain'
]
templates_path = ['_templates']
source_suffix = '.rst'
master_doc = 'index'
project = u'Dogmatist'
copyright = u'2015, Bravesheep'
version = '1.0'
release = '1.0'
exclude_patterns = ['_build']
default_role = 'obj'
pygments_style = 'sphinx'
intersphinx_mapping = {
    # 'python': ('http://python.readthedocs.org/en/latest/', None),
}
# This doesn't exist since we aren't shipping any static files ourselves.
#html_static_path = ['_static']
htmlhelp_basename = 'Dogmatist'

exclude_patterns = [
    #'api' # needed for ``make gettext`` to not die.
]

language = 'en'

locale_dirs = [
    'locale/',
]
gettext_compact = False

# enable highlighting for PHP code not between ``<?php ... ?>`` by default
lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)

on_rtd = os.environ.get('READTHEDOCS', None) == 'True'
if not on_rtd:  # only import and set the theme if we're building docs locally
    import sphinx_rtd_theme
    html_theme = 'sphinx_rtd_theme'
    html_theme_path = [sphinx_rtd_theme.get_html_theme_path()]
