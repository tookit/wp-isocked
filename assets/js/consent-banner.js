/**
 * GDPR Consent Banner JavaScript
 * WordPress Custom Implementation
 */

;(function ($) {
  'use strict'

  // Consent Manager Class
  class GDPRConsentManager {
    constructor() {
      this.consentData = {
        essential: true, // Always true
        analytics: false,
        advertising: false,
        personalization: false,
        timestamp: null,
        version: '1.0',
      }

      this.banner = null
      this.modal = null
      this.debugStatus = null

      this.init()
    }

    init() {
      // Wait for DOM to be ready
      $(document).ready(() => {
        this.cacheDOMElements()
        this.bindEvents()
        this.loadStoredConsent()
      })
    }

    cacheDOMElements() {
      this.banner = $('#gdprConsentBanner')
      this.modal = $('#gdprSettingsModal')
      this.debugStatus = $('#gdprDebugStatus')

      // Banner buttons
      this.$acceptAllBtn = $('#gdprAcceptAll')
      this.$rejectAllBtn = $('#gdprRejectAll')
      this.$showSettingsBtn = $('#gdprShowSettings')

      // Modal elements
      this.$modalClose = $('#gdprModalClose')
      this.$saveSettingsBtn = $('#gdprSaveSettings')
      this.$saveRejectAllBtn = $('#gdprSaveRejectAll')

      // Consent toggles
      this.$analyticsToggle = $('#gdprAnalytics')
      this.$advertisingToggle = $('#gdprAdvertising')
      this.$personalizationToggle = $('#gdprPersonalization')
    }

    bindEvents() {
      // Banner button events
      this.$acceptAllBtn.on('click', () => this.acceptAll())
      this.$rejectAllBtn.on('click', () => this.rejectAll())
      this.$showSettingsBtn.on('click', () => this.showSettings())

      // Modal events
      this.$modalClose.on('click', () => this.hideSettings())
      this.$saveSettingsBtn.on('click', () => this.saveSettings())
      this.$saveRejectAllBtn.on('click', () => this.saveAndRejectAll())

      // Close modal when clicking overlay
      this.modal.on('click', (e) => {
        if (e.target === this.modal[0]) {
          this.hideSettings()
        }
      })

      // Keyboard navigation
      $(document).on('keydown', (e) => {
        if (e.key === 'Escape') {
          if (this.modal.hasClass('show')) {
            this.hideSettings()
          }
        }
      })

      // Accessibility: Focus management
      this.modal.on('shown', () => {
        this.$modalClose.focus()
      })
    }

    loadStoredConsent() {
      $.ajax({
        url: gdprAjax.ajaxurl,
        type: 'POST',
        data: {
          action: 'get_gdpr_consent',
          nonce: gdprAjax.nonce,
        },
        success: (response) => {
          if (response.success && response.data) {
            this.consentData = { ...this.consentData, ...response.data }

            if (this.hasValidConsent()) {
              this.applyStoredConsent()
              this.updateDebugStatus()
            } else {
              this.showBanner()
            }
          } else {
            this.showBanner()
          }
        },
        error: () => {
          console.warn('GDPR: Failed to load stored consent, showing banner')
          this.showBanner()
        },
      })
    }

    hasValidConsent() {
      if (!this.consentData.timestamp) return false

      // Check if consent is less than 12 months old
      const consentAge = Date.now() - this.consentData.timestamp
      const maxAge = 365 * 24 * 60 * 60 * 1000 // 1 year
      return consentAge < maxAge
    }

    showBanner() {
      if (this.banner.length) {
        // Add show class with slight delay for smooth animation
        setTimeout(() => {
          this.banner.addClass('show')
          this.updateDebugStatus('Banner shown')

          // Focus management for accessibility
          this.$acceptAllBtn.focus()
        }, 100)
      }
    }

    hideBanner() {
      this.banner.removeClass('show')
    }

    showSettings() {
      // Load current consent state into toggles
      this.$analyticsToggle.prop('checked', this.consentData.analytics)
      this.$advertisingToggle.prop('checked', this.consentData.advertising)
      this.$personalizationToggle.prop('checked', this.consentData.personalization)

      this.modal.addClass('show')
      this.modal.trigger('shown')
    }

    hideSettings() {
      this.modal.removeClass('show')
    }

    acceptAll() {
      this.consentData = {
        essential: true,
        analytics: true,
        advertising: true,
        personalization: true,
        timestamp: Date.now(),
        version: '1.0',
      }

      this.saveConsent(() => {
        this.applyConsent()
        this.hideBanner()
        this.updateDebugStatus('All cookies accepted')
      })
    }

    rejectAll() {
      this.consentData = {
        essential: true,
        analytics: false,
        advertising: false,
        personalization: false,
        timestamp: Date.now(),
        version: '1.0',
      }

      this.saveConsent(() => {
        this.applyConsent()
        this.hideBanner()
        this.updateDebugStatus('Non-essential cookies rejected')
      })
    }

    saveSettings() {
      this.consentData = {
        essential: true,
        analytics: this.$analyticsToggle.is(':checked'),
        advertising: this.$advertisingToggle.is(':checked'),
        personalization: this.$personalizationToggle.is(':checked'),
        timestamp: Date.now(),
        version: '1.0',
      }

      this.saveConsent(() => {
        this.applyConsent()
        this.hideBanner()
        this.hideSettings()
        this.updateDebugStatus('Custom settings saved')
      })
    }

    saveAndRejectAll() {
      this.rejectAll()
      this.hideSettings()
    }

    saveConsent(callback) {
      $.ajax({
        url: gdprAjax.ajaxurl,
        type: 'POST',
        data: {
          action: 'save_gdpr_consent',
          nonce: gdprAjax.nonce,
          analytics: this.consentData.analytics,
          advertising: this.consentData.advertising,
          personalization: this.consentData.personalization,
        },
        success: (response) => {
          if (response.success) {
            console.log('GDPR: Consent saved successfully')
            if (callback) callback()
          } else {
            console.error('GDPR: Failed to save consent')
          }
        },
        error: (xhr, status, error) => {
          console.error('GDPR: AJAX error saving consent:', error)
        },
      })
    }

    applyStoredConsent() {
      this.applyConsent()
      this.updateDebugStatus('Stored consent applied')
    }

    applyConsent() {
      // Apply Google Consent Mode v2
      if (typeof gtag !== 'undefined') {
        gtag('consent', 'update', {
          analytics_storage: this.consentData.analytics ? 'granted' : 'denied',
          ad_storage: this.consentData.advertising ? 'granted' : 'denied',
          ad_user_data: this.consentData.advertising ? 'granted' : 'denied',
          ad_personalization: this.consentData.personalization ? 'granted' : 'denied',
          personalization_storage: this.consentData.personalization ? 'granted' : 'denied',
          functionality_storage: 'granted',
          security_storage: 'granted',
        })

        console.log('GDPR: Google Consent Mode updated', this.consentData)
      }

      // Initialize or block services based on consent
      if (this.consentData.analytics) {
        this.initializeAnalytics()
      } else {
        this.blockAnalytics()
      }

      if (this.consentData.advertising) {
        this.initializeAdvertising()
      } else {
        this.blockAdvertising()
      }

      if (this.consentData.personalization) {
        this.initializePersonalization()
      } else {
        this.blockPersonalization()
      }

      // Trigger custom event for other scripts to listen to
      $(document).trigger('gdpr:consent-updated', [this.consentData])
    }

    initializeAnalytics() {
      // Initialize Google Analytics
      console.log('GDPR: Analytics initialized')

      // Example: Initialize GA4 if not already initialized
      if (typeof gtag !== 'undefined' && window.GA_MEASUREMENT_ID) {
        gtag('config', window.GA_MEASUREMENT_ID, {
          anonymize_ip: true,
          allow_google_signals: this.consentData.advertising,
        })
      }

      // Trigger custom event
      $(document).trigger('gdpr:analytics-enabled')
    }

    blockAnalytics() {
      console.log('GDPR: Analytics blocked')
      $(document).trigger('gdpr:analytics-disabled')
    }

    initializeAdvertising() {
      console.log('GDPR: Advertising services initialized')
      $(document).trigger('gdpr:advertising-enabled')
    }

    blockAdvertising() {
      console.log('GDPR: Advertising services blocked')
      $(document).trigger('gdpr:advertising-disabled')
    }

    initializePersonalization() {
      console.log('GDPR: Personalization enabled')
      $(document).trigger('gdpr:personalization-enabled')
    }

    blockPersonalization() {
      console.log('GDPR: Personalization disabled')
      $(document).trigger('gdpr:personalization-disabled')
    }

    updateDebugStatus(message) {
      if (this.debugStatus.length) {
        const acceptedCount = Object.keys(this.consentData)
          .filter((key) => key !== 'essential' && key !== 'timestamp' && key !== 'version')
          .filter((key) => this.consentData[key]).length

        const status = message || `${acceptedCount} categories accepted`
        this.debugStatus.text(`Consent: ${status}`)

        // Update background color based on consent
        if (this.hasValidConsent()) {
          this.debugStatus.css('background-color', '#00a32a')
        } else {
          this.debugStatus.css('background-color', '#d63638')
        }
      }
    }

    // Public method to get current consent state
    getConsentData() {
      return { ...this.consentData }
    }

    // Public method to check specific consent
    hasConsent(category) {
      return this.consentData[category] === true
    }

    // Public method to reset consent (for testing)
    resetConsent() {
      $.ajax({
        url: gdprAjax.ajaxurl,
        type: 'POST',
        data: {
          action: 'reset_gdpr_consent',
          nonce: gdprAjax.nonce,
        },
        success: () => {
          location.reload()
        },
      })
    }
  }

  // Initialize the consent manager
  window.gdprConsentManager = new GDPRConsentManager()

  // Expose public API
  window.GDPR = {
    getConsent: (category) => window.gdprConsentManager.hasConsent(category),
    getConsentData: () => window.gdprConsentManager.getConsentData(),
    showSettings: () => window.gdprConsentManager.showSettings(),
    resetConsent: () => window.gdprConsentManager.resetConsent(),
  }

  // Example usage for other scripts:
  // $(document).on('gdpr:consent-updated', function(event, consentData) {
  //     if (consentData.analytics) {
  //         // Initialize analytics
  //     }
  // });
})(jQuery)
