/**
 * ThemeProvider
 * Applies CSS custom properties from localStorage and binds theme inputs.
 */
class ThemeProvider {
  static STORAGE_KEY = 'siteTheme';
  static DEFAULTS = {};

  static themeInputsPresent() {
    return document.querySelector('[data-theme-var]') !== null;
  }

  static init() {
    if (!ThemeProvider.themeInputsPresent()) {
      return;
    }

    const rootStyles = getComputedStyle(document.documentElement);
    const inputs = Array.from(document.querySelectorAll('[data-theme-var]'));

    inputs.forEach((input) => {
      const themeVar = input.dataset.themeVar;
      if (themeVar) {
        ThemeProvider.DEFAULTS[themeVar] = rootStyles.getPropertyValue(themeVar).trim() || '';
      }
    });

    const savedTheme = localStorage.getItem(ThemeProvider.STORAGE_KEY);
    if (!savedTheme) {
      return;
    }

    try {
      const theme = JSON.parse(savedTheme);
      ThemeProvider.apply(theme, false);
      ThemeProvider.syncInputs(theme);
    } catch (error) {
      console.warn('ThemeProvider: invalid saved theme data', error);
      localStorage.removeItem(ThemeProvider.STORAGE_KEY);
    }
  }

  static apply(theme, persist = true) {
    const root = document.documentElement;

    Object.entries(theme).forEach(([key, value]) => {
      if (typeof value !== 'string') {
        return;
      }
      root.style.setProperty(key, value);
    });

    if (persist && ThemeProvider.themeInputsPresent()) {
      try {
        localStorage.setItem(ThemeProvider.STORAGE_KEY, JSON.stringify(theme));
      } catch (error) {
        console.warn('ThemeProvider: unable to persist theme', error);
      }
    }

    ThemeProvider.syncInputs(theme);
  }

  static reset() {
    ThemeProvider.apply(ThemeProvider.DEFAULTS);
  }

  static syncInputs(theme, root = document) {
    const inputs = Array.from(root.querySelectorAll('[data-theme-var]'));
    inputs.forEach((input) => {
      const themeVar = input.dataset.themeVar;
      if (!themeVar) {
        return;
      }

      const value = theme[themeVar] || getComputedStyle(document.documentElement).getPropertyValue(themeVar).trim();
      if (value) {
        input.value = value;
      }
    });
  }

  static bindInputs(root = document) {
    const inputs = Array.from(root.querySelectorAll('[data-theme-var]'));

    inputs.forEach((input) => {
      const themeVar = input.dataset.themeVar;
      if (!themeVar) {
        return;
      }

      const applyValue = () => {
        const value = input.value;
        if (!value) {
          return;
        }
        ThemeProvider.apply({ [themeVar]: value });
      };

      input.addEventListener('input', applyValue);
      input.addEventListener('change', applyValue);
    });
  }
}

window.ThemeProvider = ThemeProvider;

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    ThemeProvider.init();
    ThemeProvider.bindInputs();
  });
} else {
  ThemeProvider.init();
  ThemeProvider.bindInputs();
}
