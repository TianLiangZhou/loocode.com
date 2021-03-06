import {EventEmitter, Injectable} from "@angular/core";
import {DynamicScriptLoaderService} from "./dynamic.script.loader.service";

@Injectable()
export class CKFinderService {
  private finderFileChoose = new EventEmitter<any>();
  constructor(private dynamicScript: DynamicScriptLoaderService) {
    if (window['CKFinder'] == undefined) {
      this.dynamicScript.loadCKfinder();
    }
  }
  subscribe<T>(func: (value: T) => void) {
    this.finderFileChoose.subscribe(func);
  }
  popup(element: string, resourceType = 'Images', multi: boolean = true, origin: string = "") {
    const _this = this;
    // @ts-ignore
    window.CKFinder.modal({
      language: 'zh-cn',
      resourceType: resourceType,
      chooseFiles: true,
      selectMultiple: multi,
      onInit: function (finder) {
        finder.on('files:choose', function (evt) {
          const files: { name: string, url: string, pixel: string, size: number, extension: string, origin: string}[] = [];
          evt.data.files.forEach((item, index) => {
            files[index] = {
              origin: origin,
              name: item.attributes.name,
              url: item.attributes.url,
              size: item.attributes.size,
              extension: item._extenstion,
              pixel: item.attributes.hasOwnProperty('imageResizeData')
                ? item.attributes.imageResizeData.attributes.originalSize
                : ''
            };
          });
          _this.finderFileChoose.emit(files);
          document.dispatchEvent(new MouseEvent('click'));
          if (element) {
            (<HTMLInputElement>document.getElementById(element)).value = evt.data.files.last().getUrl();
          }
        });
      }
    });
  }
}
