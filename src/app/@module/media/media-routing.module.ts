import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {MediaLibraryComponent} from "./media-library/media-library.component";

const routes: Routes = [
  {
    path: "library",
    component: MediaLibraryComponent,
    data: {title: "媒体库"}
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MediaRoutingModule { }
