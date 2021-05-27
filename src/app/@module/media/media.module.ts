import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MediaRoutingModule } from './media-routing.module';
import { MediaLibraryComponent } from './media-library/media-library.component';
import {NbCardModule} from "@nebular/theme";


@NgModule({
  declarations: [MediaLibraryComponent],
  imports: [
    CommonModule,
    MediaRoutingModule,
    NbCardModule
  ]
})
export class MediaModule { }
