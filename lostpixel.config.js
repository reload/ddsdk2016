module.exports = {
    pageShots: {
      pages: [
        { path: '/arrangementer?search_text=telt&event_after=21%2F09%2F2023&event_before=30%2F09%2F2023', name: 'arrangementer' },
        { path: '/aktivitet/1-dags-kollektiv', name: 'aktivitet' },
        { path: '/maerker?maalgruppe=30', name: 'mærker' },
        { path: '/maerke/bygherre', name: 'mærke' },
        { path: '/artikel/guide-sadan-pakker-du-rygsaekken', name: 'artikel' },
      ],
      baseUrl: process.env.LOST_PIXEL_BASE_URL,
    },
    lostPixelProjectId: 'clm87pkuu9umnln0eu69t81eu',
    apiKey: process.env.LOST_PIXEL_API_KEY,
    waitBeforeScreenshot: 2000,
    beforeScreenshot: async (page) => {

      await page.addStyleTag({
        content: `
          #CybotCookiebotDialog,
          #CybotCookiebotDialog[style] {
            display: none !important;
            visibility: hidden !important;
          }
        `,
      })
    },
};
