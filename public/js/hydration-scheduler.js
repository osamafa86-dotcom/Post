/**
 * hydration-scheduler.js — requestIdleCallback task queue
 * Spreads work across idle frames. No single task > 50ms.
 * Exposes window.__schedule(fn) for use by main-interactions.js
 */
(function () {
  'use strict';

  var queue = [];
  var running = false;

  // Safari fallback (no native requestIdleCallback)
  var ric = window.requestIdleCallback || function (cb) {
    return setTimeout(function () {
      var start = Date.now();
      cb({
        didTimeout: false,
        timeRemaining: function () { return Math.max(0, 50 - (Date.now() - start)); }
      });
    }, 1);
  };

  function schedule(task) {
    queue.push(task);
    run();
  }

  function run() {
    if (running) return;
    running = true;
    ric(process, { timeout: 200 });
  }

  function process(deadline) {
    while ((deadline.timeRemaining() > 8 || deadline.didTimeout) && queue.length) {
      var job = queue.shift();
      try { job(); } catch (e) { console.warn('[scheduler]', e); }
    }

    if (queue.length) {
      ric(process, { timeout: 200 });
    } else {
      running = false;
    }
  }

  window.__schedule = schedule;
})();
