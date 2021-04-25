#include <stdarg.h>
#include <stdbool.h>
#include <stdint.h>
#include <stdlib.h>

typedef struct QR QR;

typedef struct Cfg {
  const char *bg_color;
  const char *fg_color;
  const char *filename;
  int dimension_w;
  int dimension_h;
  const char *logo;
  int filter_transparent;
  int auto_resize;
  int zone;
} Cfg;

struct QR *create(const char *str);

char *image(struct QR *qr, struct Cfg *cfg);

char *svg(struct QR *qr, struct Cfg *cfg);

char *character(struct QR *qr, struct Cfg *cfg);

char *unicode(struct QR *qr, struct Cfg *cfg);

void free_image(char *ptr);

/**
 * Destroy a `Response` once you are done with it.
 */
void free_qrcode(struct QR *res);

int last_error_length(void);

int last_error_message(char *buffer, int length);
