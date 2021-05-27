import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ModuleComponent } from './module.component';
import { DashboardComponent } from './dashboard/dashboard.component';

const routes: Routes = [{
  path: '',
  component: ModuleComponent,
  children: [
    {
      path: 'dashboard',
      component: DashboardComponent,
      data: {'title': 'Dashboard'},
    },
    {
      path: 'system',
      loadChildren: () => import('./system/system.module').then(m => m.SystemModule),
    },
    {
      path: 'user',
      loadChildren: () => import('./user/user.module').then(m => m.UserModule),
    },
    {
      path: 'content',
      loadChildren: () => import('./content/content.module').then(m => m.ContentModule.forRoot().ngModule),
    },
    {
      path: 'page',
      loadChildren: () => import('./page/page.module').then(m => m.PageModule),
    },
    {
      path: 'decoration',
      loadChildren: () => import('./decoration/decoration.module').then(m => m.DecorationModule),
    },
    {
      path: 'extension',
      loadChildren: () => import('./extension/extension.module').then(m => m.ExtensionModule),
    },
    {
      path: 'media',
      loadChildren: () => import('./media/media.module').then(m => m.MediaModule),
    }
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ModuleRoutingModule { }
